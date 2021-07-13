/*
 * Populate a newly created newsledger database with the minimal data
 * required to make it functional.
 */

INSERT INTO
    `configuration`
        (`key`, `value`)
    VALUES
        ('billing-period', '2'),
        ('customer-billing-type', 'manual'),
        ('flag-stop-type', '1');

INSERT INTO
    `customers_types`
        (`id`, `abbr`, `name`, `color`, `visible`, `newChange`, `watchStart`, `su`, `mo`, `tu`, `we`, `th`, `fr`, `sa`)
    VALUES
        (NULL, 'FS', 'Flag Stop', 14544639, 'Y', 'N', 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y'),
        (NULL, 'DS', 'Daily + Sunday', 16777215, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y'),
        (NULL, 'DO', 'Daily Only', 13434751, 'Y', 'Y', 'Y', 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y'),
        (NULL, 'SO', 'Sunday Only', 16756912, 'Y', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'N', 'N');

INSERT INTO
    `groups`
        (`id` , `name`)
    VALUES
        (NULL, 'Administrators');

INSERT INTO
    `periods`
        (`id`, `created`, `updated`, `changes_start`, `changes_end`, `bill`, `display_start`, `display_end`, `due`, `title`)
    VALUES
        (NULL, NOW(), CURRENT_TIMESTAMP , '2010-09-01', '2010-09-30', '2010-10-01', '2010-09-01', '2010-09-30', '2010-10-10', 'September 2010'),
        (NULL, NOW(), CURRENT_TIMESTAMP , '2010-10-01', '2010-10-31', '2010-11-01', '2010-10-01', '2010-10-31', '2010-11-10', 'October 2010'),
        (NULL, NOW(), CURRENT_TIMESTAMP , '2010-11-01', '2010-11-30', '2010-12-01', '2010-11-01', '2010-11-30', '2010-12-10', 'November 2010');

INSERT INTO
    `routes`
        (`id`, `created`, `updated`, `title`, `active`)
    VALUES
        (NULL, NOW( ), CURRENT_TIMESTAMP , 'A', 'Y');

INSERT INTO
    `security`
        (`group_id`, `user_id`, `page`, `feature`, `allowed`)
    VALUES
        (NULL, NULL, '30903', 'page', 'N');

INSERT INTO
    `users`
        (`id`, `login`, `password`, `name`, `group_id`, `home`)
    VALUES
        (NULL, 'admin', MD5('p@ssw0rd'), 'Administrator', '1', '');

