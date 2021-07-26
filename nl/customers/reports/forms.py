
from flask_wtf import FlaskForm
from wtforms import (
    # DecimalField,
    # FormField,
    HiddenField,
    IntegerField,
    RadioField,
    SelectField,
    StringField,
    # SubmitField,
    # TextAreaField,
)
from wtforms.fields.html5 import DateField, TimeField
from wtforms.validators import Optional, Required

from nl.utils import ignore_yes_no


__all__ = [
    'AheadForm',
    'BehindForm',
    'InactiveForm',
    'OrdersForm'
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
    active = SelectField('Active', choices=ignore_yes_no)
    pending = SelectField('With Pending Stops', choices=ignore_yes_no)
    routeList = SelectField('Route List', choices=ignore_yes_no)
    stopped = IntegerField('Last Stopped')


class OrdersForm(FlaskForm):
    action = HiddenField()
    when = DateField('Week that includes')
    
