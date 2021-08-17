
from flask_wtf import FlaskForm
from wtforms import (
    BooleanField,
    # CheckboxField,
    # DecimalField,
    # DecimalRangeField,
    # EmailField,
    # FieldList,
    # FileField,
    # FloatField,
    # FormField,
    HiddenField,
    IntegerField,
    # IntegerRangeField,
    # MultipleFileField,
    # PasswordField,
    # RadioField,
    # SearchField,
    SelectField,
    # SelectMultipleField,
    StringField,
    # SubmitField,
    # TelField,
    # TextAreaField,
    # URLField,
)
from wtforms.fields.html5 import (
    DateField,
    TimeField,
)
from wtforms.validators import Optional, Required

from nl.utils import ignore_yes_no


__all__ = [
    'AheadForm',
    'BehindForm',
    'InactiveForm',
    'InfoForm',
]


class AheadForm(FlaskForm):
    action = HiddenField()
    count = StringField()
    many = IntegerField(validators=[Optional()])


class BehindForm(FlaskForm):
    action = HiddenField()
    what = StringField()
    
    
class InactiveForm(FlaskForm):
    action = HiddenField()
    routeList = SelectField('Route List', choices=ignore_yes_no)


class InfoForm(FlaskForm):
    action = HiddenField()
    adjustments = BooleanField('Adjustments')
    billing = BooleanField('Billing')
    billing_note = BooleanField('Billing Note')
    balance = BooleanField('Balance')
    delivery_note = BooleanField('Delivery Note')
    id_ = BooleanField('Customer ID')
    notes = BooleanField('Notes')
    payments = BooleanField('Payments')
    rate = BooleanField('Rate')
    route = BooleanField('Route')
    #service = BooleanField('Service Changes')

