
from flask import render_template, flash


__all__ = [
    'customer_type_choices',
    'flash_fail',
    'flash_success',
    'ignore_yes_no',
    'money_choices',
    'name_title_choices',
    'pagination',
    'payment_type_choices',
    'payment_type_op_choices',
    'period_choices'
    'state_choices',
    'telephone_type_choices',
]


def customer_type_choices(any=True):
    """
    Return list of (CustomerTypes.id, CustomerTypes.abbr).

    Intended to be passed to Select widget as list of choices to display.
    """
    from nl.models import CustomerTypes
    types = []
    if any:
        types.append([0, 'Any'])
    for type in CustomerTypes.query.filter_by(visible='Y').order_by(CustomerTypes.abbr).all():
        types.append([type.id, type.abbr])
    return types


def flash_success(message):
    flash('<span class="success">' + message + '</span>')

def flash_fail(message):
    flash('<span class="fail">' + message + '</span>')
    
# Select choices
ignore_yes_no = [(0, 'Ignore'),
                 (1, 'Yes'),
                 (2, 'No')]


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
               Default it False.       
    """

    prefix = kwargs.pop('prefix', 'dbf_')
    offset = kwargs.pop('offset', 0)
    limit = kwargs.pop('limit', 10)
    max_ = kwargs.pop('max', 0)
    left = kwargs.pop('left', False)
    refresh = kwargs.pop('refresh', True)
    clear = kwargs.pop('clear', True)

    return render_template('pagination.html', left=left, clear=clear, refresh=refresh,
                           prefix=prefix, offset=offset, limit=limit, max_=max_)


money_choices = [
    (0, '>='),
    (1, '>'),
    (2, '='),
    (3, '<'),
    (4, '<=')
]

name_title_choices = [
    (0, ''),
    (1, 'Mr'),
    (2, 'Mrs'),
    (3, 'Ms'),
    (4, 'Miss')
]


payment_type_choices = [
    (0, 'Check'),
    (1, 'Money Order'),
    (2, 'Cash'),
    (3, 'Credit')
]


payment_type_op_choices = payment_type_choices + [(99, 'Any')]


def period_choices():
    # TODO:  Yeah, maybe ... ;-)
    return [(0, 'Any')]


def route_choices(any=True):
    """
    Return list of (Route.id, Route.title).

    Intended to be passed to Select widget as list of choices to display.
    """
    from nl.models import Route
    routes = []
    if any:
        routes.append([0, 'Any'])
    for route in Route.query.filter_by(active='Y').order_by(Route.title).all():
        routes.append([route.id, route.title])
    return routes


state_choices = [
    ('AL', 'Alabama'),
    ('AK', 'Alaska'),
    ('AZ', 'Arizona'),
    ('AR', 'Arkansas'),
    ('CA',  'California'),
    ('CO', 'Colorado'),
    ('CT', 'Connecticut'),
    ('DE', 'Delaware'),
    ('FL', 'Florida'),
    ('GA', 'Georgia'),
    ('HI', 'Hawaii'),
    ('ID', 'Idaho'),
    ('IL', 'Illinois'),
    ('IN', 'Indiana'),
    ('IA', 'Iowa'),
    ('KS', 'Kansas'),
    ('KY', 'Kentucky'),
    ('LA', 'Louisiana'),
    ('ME', 'Maine'),
    ('MD', 'Maryland'),
    ('MA', 'Massachusetts'),
    ('MI', 'Michigan'),
    ('MN', 'Minnesota'),
    ('MS', 'Mississippi'),
    ('MO', 'Missouri'),
    ('MT', 'Montana'),
    ('NE', 'Nebraska'),
    ('NV', 'Nevada'),
    ('NH', 'New Hampshire'),
    ('NJ', 'New Jersey'),
    ('NM', 'New Mexico'),
    ('NY', 'New York'),
    ('NC', 'North Carolina'),
    ('ND', 'North Dakota'),
    ('OH', 'Ohio'),
    ('OK', 'Oklahoma'),
    ('OR', 'Oregon'),
    ('PA', 'Pennsylvania'),
    ('PR', 'Puerto Rico '),
    ('RI', 'Rhode Island'),
    ('SC', 'South Carolina'),
    ('SD', 'South Dakota'),
    ('TN', 'Tennessee'),
    ('TX', 'Texas'),
    ('UT', 'Utah'),
    ('VT', 'Vermont'),
    ('VI', 'Virgin Islands'),
    ('VA', 'Virginia'),
    ('WA', 'Washington'),
    ('WV', 'West Virginia'),
    ('WI', 'Wisconsin'),
    ('WY', 'Wyoming')
]


telephone_type_choices = [
    'Main',
    'Alternate',
    'Mobile',
    'Evening',
    'Day',
    'Office',
    'Message',
    'Pager',
    'Business',
    'Mobile (Office)',
    'Mobile (Business)',
    'Mobile (Day)',
    'Mobile (Evening)'
]

