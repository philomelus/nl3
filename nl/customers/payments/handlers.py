
from decimal import Decimal
from datetime import date, datetime

from flask import redirect, render_template, url_for
from flask_login import login_required

from nl import db
from nl.utils import flash_success, pagination, PaymentType, period_choices
from nl.customers.payments import bp
from nl.customers.payments.forms import AddNewForm, SearchForm


@bp.route('/addnew', methods=('GET', 'POST'))
@login_required
def addnew():
    form = AddNewForm()
    if form.validate_on_submit():
        from nl.models import Configuration, Customer, CustomerPayments

        p = CustomerPayments()
        p.customer_id = int(form.customer.data)
        p.created = datetime.today()
        type_ = form.type_.data
        if type_ == PaymentType.CHECK.value:
            p.type = 'CHECK'
            p.extra1 = form.id_.data
            p.extra2 = ''
        elif type_ == PaymentType.MONEYORDER.value:
            p.type = 'MONEYORDER'
            p.extra1 = form.id_.data
            p.extra2 = ''
        elif type_ == PaymentType.CASH.value:
            p.type = 'CASH'
            p.extra1 = p.extra2 = ''
        elif type_ == PaymentType.CREDIT.value:
            p.type = 'CREDIT'
            p.extra1 = p.extra2 = ''
        p.amount = form.amount.data
        tip = form.tip.data
        if tip is None:
            tip = 0
        p.tip = tip
        p.notes = form.notes.data
        p.period_id = Configuration.get('billing-period')
        p.date = date.today()
        db.session.add(p)

        c = Customer.query.filter_by(id=p.customer_id).first()
        c.balance = c.balance - Decimal.from_float(p.amount) - Decimal.from_float(p.tip)
        db.session.commit()

        flash_success(f'Added payment of {p.amount} to customer {p.customer_id}')

        return redirect(url_for('customers.payments.addnew'))
    
    return render_template('customers/payments/addnew.html', path='Customers / Payments / Add',
                           form=form)


@bp.route('/search', methods=('GET', 'POST'))
@login_required
def search():
    form = SearchForm()
    form.period.choices = period_choices()

    offset = 0
    limit = 10
    count = 0
    
    return render_template('customers/payments/search.html', path='Customers / Payments / Search',
                           form=form, paginate=pagination(offset=offset, limit=limit, max=count))

