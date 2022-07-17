from enum import Enum

from flask import render_template, flash


__all__ = [
    "ComplaintResult",
    "ComplaintType",
    "customer_type_choices",
    "flash_fail",
    "flash_success",
    "ignore_yes_no",
    "money_ops_choices",
    "MoneyOps",
    "name_title_choices",
    "pagination",
    "PaymentType",
    "period_choices" "state_choices",
    "telephone_type_choices",
]


class ComplaintResult(Enum):
    """
    Methods to resolve customer complaints.
    """

    CHARGE = 0
    CREDIT1DAILY = 1
    CREDIT1SUNDAY = 2
    CREDIT = 3
    NOTHING = 4
    REDELIVERED = 5

    @staticmethod
    def choices():
        """
        Return list of ComplaintResults value to human readable value
        (suitable for WTForms choices).
        """
        return [
            # (ComplaintResult.CHARGE.value, 'Charge'),
            # (ComplaintResult.CREDIT.value, 'Credit'),
            (ComplaintResult.CREDIT1DAILY.value, "Credit 1 Daily"),
            (ComplaintResult.CREDIT1SUNDAY.value, "Credit 1 Sunday"),
            (ComplaintResult.NOTHING.value, "Do Nothing"),
            (ComplaintResult.REDELIVERED.value, "Redelivered"),
        ]

    @staticmethod
    def from_db(result: int):
        """
        Return db enum corresponding to ComplaintResult value.
        """
        if result == "NONE":
            return ComplaintResult.NOTHING.value
        elif result == "CREDITDAILY":
            return ComplaintResult.CREDIT1DAILY.value
        elif result == "CREDITSUNDAY":
            return ComplaintResult.CREDIT1SUNDAY.value
        elif result == "REDELIVERED":
            return ComplaintResult.REDELIVERED.value
        elif result == "CREDIT":
            return ComplaintResult.CREDIT.value
        elif result == "CHARGE":
            return ComplaintResult.CHARGE.value
        else:
            raise ValueError(f"Not a ComplaintResult db enum: {result}")

    @staticmethod
    def to_db(result: str):
        """
        Return corresponding ComplaintResult value given db enum.
        """
        if result == ComplaintResult.NOTHING.value:
            return "NONE"
        elif result == ComplaintResult.CREDIT1DAILY.value:
            return "CREDITDAILY"
        elif result == ComplaintResult.CREDIT1SUNDAY.value:
            return "CREDITSUNDAY"
        elif result == ComplaintResult.CREDIT.value:
            return "CREDIT"
        elif result == ComplaintResult.REDELIVERED.value:
            return "REDELIVERED"
        elif result == ComplaintResult.CHARGE.value:
            return "CHARGE"
        else:
            raise ValueError(f"Not a ComplaintResult: {result}")


class ComplaintType(Enum):
    """
    Types of customer complaints.
    """

    MISSED = 0
    WET = 1
    DAMAGED = 2

    @staticmethod
    def choices():
        """
        Return list of ComplaintTypes value to human readable value
        (suitable for WTForms choices).
        """
        return [
            (ComplaintType.MISSED.value, "Missed"),
            (ComplaintType.WET.value, "Wet"),
            (ComplaintType.DAMAGED.value, "Damaged"),
        ]

    @staticmethod
    def to_db(type: int):
        """
        Return db enum corresponding to ComplaintType value.
        """
        if type == ComplaintType.MISSED.value:
            return "MISSED"
        elif type == ComplaintType.WET.value:
            return "WET"
        elif type == ComplaintType.DAMAGED.value:
            return "DAMAGED"
        else:
            raise ValueError(f"Not a ComplaintType db enum: {type}")

    @staticmethod
    def from_db(type: str):
        """
        Return corresponding ComplaintType value given db enum.
        """
        if type == "MISSED":
            return ComplaintType.MISSED.value
        elif type == "WET":
            return ComplaintType.WET.value
        elif type == "DAMAGED":
            return ComplaintType.DAMAGED.value
        else:
            raise ValueError("Not a ComplaintType: {type}")


def customer_type_choices(any=True):
    """
    Return list of (CustomerTypes.id, CustomerTypes.abbr).

    Intended to be passed to Select widget as list of choices to display.
    """
    from nl.models.customers import Type

    types = []
    if any:
        types.append([0, "Any"])
    for type in Type.query.filter_by(visible="Y").order_by(Type.abbr).all():
        types.append([type.id, type.abbr])
    return types


def flash_success(message, inline=False):
    if inline:
        return '<div class="flash"><span class="success">' + message + "</span></div>"
    flash('<span class="success">' + message + "</span>")


def flash_fail(message, inline=False):
    if inline:
        return '<div class="flash"><span class="fail">' + message + "</span></div>"
    flash('<span class="fail">' + message + "</span>")


# Select choices
ignore_yes_no = [(0, "Ignore"), (1, "Yes"), (2, "No")]


def pagination(**kwargs):
    """
    Returns HTML for form that contains controls for moving through records.
    Don't forget to mark as |safe if passed to jinja template.

    Possible arguments are:
        offset = Number of records to skip.
                 Default is 0.
        limit = Number of recirds to show.
                Default is 10.
        max = Total number of records (used to enable/disable next/previous).
              Default is 0.
        refresh = True to contain a "Refresh" button.
                  Default is True.
        clear = True to contain a "Clear" button.
                Default is True.
        left = True to place Refresh/Clear buttons on left side.
               Default is False.
    """

    prefix = kwargs.pop("prefix", "dbf_")
    offset = kwargs.pop("offset", 0)
    limit = kwargs.pop("limit", 10)
    max_ = kwargs.pop("max", 0)
    left = kwargs.pop("left", False)
    refresh = kwargs.pop("refresh", True)
    clear = kwargs.pop("clear", True)

    return render_template(
        "pagination.html",
        left=left,
        clear=clear,
        refresh=refresh,
        prefix=prefix,
        offset=offset,
        limit=limit,
        max_=max_,
    )


class MoneyOps(Enum):
    """
    Comparison operators for monetary values.
    """

    GREATER_EQUAL = 0
    GREATER = 1
    EQUAL = 2
    LESS = 3
    LESS_EQUAL = 4

    @staticmethod
    def choices():
        """
        Return list of MoneyOp value to human readable value
        (suitable for WTForms choices).
        """
        return [
            (MoneyOps.GREATER_EQUAL.value, ">="),
            (MoneyOps.GREATER.value, ">"),
            (MoneyOps.EQUAL.value, "="),
            (MoneyOps.LESS.value, "<"),
            (MoneyOps.LESS_EQUAL.value, "<="),
        ]


name_title_choices = [(0, ""), (1, "Mr"), (2, "Mrs"), (3, "Ms"), (4, "Miss")]


class PaymentType(Enum):
    """
    Methods customers can pay.
    """

    CHECK = 0
    MONEYORDER = 1
    CASH = 2
    CREDIT = 3

    @staticmethod
    def choices():
        return [
            (PaymentType.CHECK.value, "Check"),
            (PaymentType.MONEYORDER.value, "Money Order"),
            (PaymentType.CASH.value, "Cash"),
            (PaymentType.CREDIT.value, "Credit"),
        ]

    @staticmethod
    def ops_choices():
        return [
            (99, "Any"),
        ] + cls.choices()


def period_choices(any=False):
    """
    Return list of (id, title) for periods.

    any = If true, add 'Any' as first element of list, with value 0.
    """

    from nl.models import Period

    opts = []
    if any:
        opts.append((0, "Any"))
    for period in Period.query.all():
        opts.append((period.id, period.title))

    return opts


def route_choices(any=True):
    """
    Return list of (Route.id, Route.title).

    Intended to be passed to Select widget as list of choices to display.
    """
    from nl.models.routes import Route

    routes = []
    if any:
        routes.append([0, "Any"])
    for route in Route.query.filter_by(active="Y").order_by(Route.title).all():
        routes.append([route.id, route.title])
    return routes


state_choices = [
    ("AL", "Alabama"),
    ("AK", "Alaska"),
    ("AZ", "Arizona"),
    ("AR", "Arkansas"),
    ("CA", "California"),
    ("CO", "Colorado"),
    ("CT", "Connecticut"),
    ("DE", "Delaware"),
    ("FL", "Florida"),
    ("GA", "Georgia"),
    ("HI", "Hawaii"),
    ("ID", "Idaho"),
    ("IL", "Illinois"),
    ("IN", "Indiana"),
    ("IA", "Iowa"),
    ("KS", "Kansas"),
    ("KY", "Kentucky"),
    ("LA", "Louisiana"),
    ("ME", "Maine"),
    ("MD", "Maryland"),
    ("MA", "Massachusetts"),
    ("MI", "Michigan"),
    ("MN", "Minnesota"),
    ("MS", "Mississippi"),
    ("MO", "Missouri"),
    ("MT", "Montana"),
    ("NE", "Nebraska"),
    ("NV", "Nevada"),
    ("NH", "New Hampshire"),
    ("NJ", "New Jersey"),
    ("NM", "New Mexico"),
    ("NY", "New York"),
    ("NC", "North Carolina"),
    ("ND", "North Dakota"),
    ("OH", "Ohio"),
    ("OK", "Oklahoma"),
    ("OR", "Oregon"),
    ("PA", "Pennsylvania"),
    ("PR", "Puerto Rico "),
    ("RI", "Rhode Island"),
    ("SC", "South Carolina"),
    ("SD", "South Dakota"),
    ("TN", "Tennessee"),
    ("TX", "Texas"),
    ("UT", "Utah"),
    ("VT", "Vermont"),
    ("VI", "Virgin Islands"),
    ("VA", "Virginia"),
    ("WA", "Washington"),
    ("WV", "West Virginia"),
    ("WI", "Wisconsin"),
    ("WY", "Wyoming"),
]


telephone_type_choices = [
    "Main",
    "Alternate",
    "Mobile",
    "Evening",
    "Day",
    "Office",
    "Message",
    "Pager",
    "Business",
    "Mobile (Office)",
    "Mobile (Business)",
    "Mobile (Day)",
    "Mobile (Evening)",
]
