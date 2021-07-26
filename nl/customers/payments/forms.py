
from flask_wtf import FlaskForm
from wtforms import (StringField, SelectField, IntegerField, HiddenField,
                     SubmitField, FormField, TextAreaField, DecimalField)
from wtforms.fields.html5 import DateField, TimeField
from wtforms.validators import Optional, Required

from nl.utils import payment_type_choices, payment_type_op_choices, money_choices


__all__ = ['AddNewForm', 'SearchForm']


class AddNewForm(FlaskForm):
    action = HiddenField()

    customer = IntegerField('Customer ID', validators=[Required()])
    type_ = SelectField('Type', validators=[Required()], choices=payment_type_choices)
    id = StringField('ID', validators=[Optional()])
    amount = DecimalField('Amount', validators=[Required()], places=2)
    tip = DecimalField('Tip', validators=[Optional()], places=2)
    notes = TextAreaField('Notes', validators=[Optional()])


class SearchForm(FlaskForm):
    action = HiddenField()
    limit = IntegerField()
    offset = IntegerField()

    after_date = DateField('After', validators=[Optional()])
    after_time = TimeField(validators=[Optional()])
    amount = DecimalField('Amount', validators=[Optional()], places=2)
    amount_op = SelectField(validators=[Optional()], choices=money_choices)
    before_date = DateField('Before', validators=[Optional()])
    before_time = TimeField(validators=[Optional()])
    customer = StringField('Customer ID', validators=[Optional()])
    id_ = StringField('ID', validators=[Optional()])
    notes = StringField('Notes', validators=[Optional()])
    payment = StringField('Payment ID', validators=[Optional()])
    period = SelectField('Period', validators=[Optional()])
    tip = DecimalField('Tip', validators=[Optional()], places=2)
    tip_op = SelectField(validators=[Optional()], choices=money_choices)
    type_ = SelectField('Type', validators=[Optional()], choices=payment_type_op_choices)
    
