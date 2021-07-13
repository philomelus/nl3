SELECT
  (
    SELECT `allowed` FROM `security`
    WHERE
      `user_id` = 5
      AND (ISNULL(`group_id`) OR `group_id` = 2)
      AND `page` = 4
      AND `feature` = 'page'
    LIMIT 1
  ) AS `user`,
  (
    SELECT `allowed` FROM `security`
    WHERE
      `group_id` = 2
      AND ISNULL(`user_id`)
      AND `page` = 4
      AND `feature` = 'page'
    LIMIT 1
  ) AS `group`,
  (
    SELECT `allowed` FROM `security`
    WHERE
      ISNULL(`group_id`)
      AND ISNULL(`user_id`)
      AND `page` = 4
      AND `feature` = 'page'
    LIMIT 1
  ) AS `global`
