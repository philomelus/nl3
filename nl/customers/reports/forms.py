
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

