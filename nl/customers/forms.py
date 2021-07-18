
from flask_wtf import FlaskForm
from wtforms import StringField, SelectField, IntegerField, HiddenField, SubmitField
from wtforms.validators import Optional

from nl.models import Customer
from nl.utils import ignore_yes_no


class SearchForm(FlaskForm):
    action = HiddenField()
    address = StringField('Address', validators=[Optional(),])
    billing = SelectField('In Billinig', validators=[Optional(),])
    customer = IntegerField('Customer ID', validators=[Optional(),])
    dtype = SelectField('Type', validators=[Optional(),])
    limit = IntegerField()
    name = StringField('Name', validators=[Optional(),])
    offset = IntegerField()
    postal = StringField('Zip', validators=[Optional(),])
    route = SelectField('Route', validators=[Optional(),])
    routeList = SelectField('In Route List', validators=[Optional(),])
    telephone = StringField('Telephone', validators=[Optional(),])

