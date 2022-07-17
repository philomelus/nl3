from flask_wtf import FlaskForm
from wtforms import (
    StringField,
    SelectField,
    IntegerField,
    HiddenField,
    SubmitField,
    FormField,
    TextAreaField,
)
from wtforms.fields.html5 import DateField
from wtforms.validators import Optional

from nl.models.customers import Customer
from nl.utils import (
    ignore_yes_no,
    name_title_choices,
    state_choices,
    telephone_type_choices,
)


__all__ = ["CreateForm", "SearchForm"]


class AddressForm(FlaskForm):
    address1 = StringField("Address")
    address2 = StringField("Address 2")
    city = StringField("City")
    state = SelectField("Sate", choices=state_choices)
    postal = StringField("Postal")


class NameForm(FlaskForm):
    title = SelectField("Title", choices=name_title_choices)
    first = StringField("First Name")
    last = StringField("Last Name")
    surname = StringField("Surname")


class TelephoneForm(FlaskForm):
    type_ = SelectField(choices=telephone_type_choices)
    number = StringField()


class DeliveryForm(FlaskForm):
    address = FormField(AddressForm)
    name_ = FormField(NameForm)
    name2 = FormField(NameForm, label="Alternate Name")
    route = SelectField("Route")
    start_date = DateField("Start Date")
    telephone1 = FormField(TelephoneForm, label="Telephone 1")
    telephone2 = FormField(TelephoneForm, label="Telephone 2")
    telephone3 = FormField(TelephoneForm, label="Telephone 3")
    dtype = SelectField("Delivery Type")


class BillingForm(FlaskForm):
    address = FormField(AddressForm)
    name_ = FormField(NameForm)
    name2 = FormField(NameForm)
    telephone1 = FormField(TelephoneForm)
    telephone2 = FormField(TelephoneForm)
    telephone3 = FormField(TelephoneForm)


class NotesForm(FlaskForm):
    billing = TextAreaField("Billing Notes")
    delivery = TextAreaField("Delivery Notes")
    notes = TextAreaField("Notes")


class CreateForm(FlaskForm):

    action = HiddenField()
    limit = IntegerField()
    offset = IntegerField()

    billing = FormField(BillingForm)
    delivery = FormField(DeliveryForm)
    notes_ = FormField(NotesForm)


class SearchForm(FlaskForm):
    action = HiddenField()
    address = StringField(
        "Address",
        validators=[
            Optional(),
        ],
    )
    billing = SelectField(
        "In Billinig",
        validators=[
            Optional(),
        ],
    )
    customer = IntegerField(
        "Customer ID",
        validators=[
            Optional(),
        ],
    )
    dtype = SelectField(
        "Type",
        validators=[
            Optional(),
        ],
    )
    limit = IntegerField()
    name = StringField(
        "Name",
        validators=[
            Optional(),
        ],
    )
    offset = IntegerField()
    postal = StringField(
        "Zip",
        validators=[
            Optional(),
        ],
    )
    route = SelectField(
        "Route",
        validators=[
            Optional(),
        ],
    )
    routeList = SelectField(
        "In Route List",
        validators=[
            Optional(),
        ],
    )
    telephone = StringField(
        "Telephone",
        validators=[
            Optional(),
        ],
    )
