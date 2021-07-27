
from flask_wtf import FlaskForm
from wtforms import (
    FloatField,
    FormField,
    HiddenField,
    IntegerField,
    SelectField,
    StringField,
    SubmitField,
    TextAreaField,
)
from wtforms.fields.html5 import DateField, TimeField
from wtforms.validators import InputRequired, Optional, ValidationError

from nl.utils import PaymentType, payment_type_choices, payment_type_op_choices, money_choices


__all__ = ['AddNewForm', 'SearchForm']


def check_id(form, field):
    if form.type_.data in (PaymentType.CHECK.value, PaymentType.MONEYORDER.value):
        if len(field.data) == 0:
            raise ValidationError("Payment ID is invalid")


class AddNewForm(FlaskForm):
    action = HiddenField()

    customer = IntegerField('Customer ID', validators=[InputRequired()])
    type_ = SelectField('Type', validators=[InputRequired()], choices=payment_type_choices, coerce=int)
    id_ = StringField('ID', validators=[check_id])
    amount = FloatField('Amount', validators=[InputRequired()])
    tip = FloatField('Tip', validators=[Optional()])
    notes = TextAreaField('Notes', validators=[Optional()])


class SearchForm(FlaskForm):
    action = HiddenField()
    limit = IntegerField()
    offset = IntegerField()

    after_date = DateField('After', validators=[Optional()])
    after_time = TimeField(validators=[Optional()])
    amount = FloatField('Amount', validators=[Optional()])
    amount_op = SelectField(validators=[Optional()], choices=money_choices)
    before_date = DateField('Before', validators=[Optional()])
    before_time = TimeField(validators=[Optional()])
    customer = StringField('Customer ID', validators=[Optional()])
    id_ = StringField('ID', validators=[Optional()])
    notes = StringField('Notes', validators=[Optional()])
    payment = StringField('Payment ID', validators=[Optional()])
    period = SelectField('Period', validators=[Optional()])
    tip = FloatField('Tip', validators=[Optional()])
    tip_op = SelectField(validators=[Optional()], choices=money_choices)
    type_ = SelectField('Type', validators=[Optional()], choices=payment_type_op_choices)
    
