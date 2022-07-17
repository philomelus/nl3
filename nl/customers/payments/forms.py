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

from nl.utils import PaymentType, MoneyOps


__all__ = ["CreateForm", "SearchForm"]


def check_id(form, field):
    if form.type_.data in (PaymentType.CHECK.value, PaymentType.MONEYORDER.value):
        if len(field.data) == 0:
            raise ValidationError("Payment ID is invalid")


class CreateForm(FlaskForm):
    action = HiddenField()
    amount = FloatField("Amount", validators=[InputRequired()])
    customer = IntegerField("Customer ID", validators=[InputRequired()])
    id_ = StringField("ID", validators=[check_id])
    notes = TextAreaField("Notes", validators=[Optional()])
    type_ = SelectField(
        "Type", validators=[InputRequired()], choices=PaymentType.choices(), coerce=int
    )
    tip = FloatField("Tip", validators=[Optional()])


class SearchForm(FlaskForm):
    action = HiddenField()
    after = DateField("After", validators=[Optional()])
    amount = FloatField("Amount", validators=[Optional()])
    amount_op = SelectField(validators=[Optional()], choices=MoneyOps.choices())
    before = DateField("Before", validators=[Optional()])
    customer = StringField("Customer ID", validators=[Optional()])
    id_ = StringField("ID", validators=[Optional()])
    limit = IntegerField()
    notes = StringField("Notes", validators=[Optional()])
    offset = IntegerField()
    payment = StringField("Payment ID", validators=[Optional()])
    period = SelectField("Period", validators=[Optional()])
    tip = FloatField("Tip", validators=[Optional()])
    tip_op = SelectField(validators=[Optional()], choices=MoneyOps.choices())
    type_ = SelectField(
        "Type", validators=[Optional()], choices=PaymentType.ops_choices()
    )
