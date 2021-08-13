
from flask import request
from flask_login import login_required

from nl import db

from nl.api.customers.payments import bp


@bp.route('/create', methods=('POST',))
@login_required
def create():
    from nl.models import Configuration, Customer, CustomerPayments

    p = CustomerPayments()
    p.customer_id = int(request.form.customer)
    p.created = datetime.now()
    type_ = int(request.form.type_)
    if type_ == PaymentType.CHECK.value:
        p.type = 'CHECK'
        p.extra1 = request.form.id_
        p.extra2 = ''
    elif type_ == PaymentType.MONEYORDER.value:
        p.type = 'MONEYORDER'
        p.extra1 = form.id_
        p.extra2 = ''
    elif type_ == PaymentType.CASH.value:
        p.type = 'CASH'
        p.extra1 = p.extra2 = ''
    elif type_ == PaymentType.CREDIT.value:
        p.type = 'CREDIT'
        p.extra1 = p.extra2 = ''
    p.amount = float(request.form.amount)
    tip = request.form.tip
    if tip is None:
        tip = 0
    p.tip = float(tip)
    p.notes = request.form.notes
    p.period_id = Configuration.get('billing-period')
    p.date = date.now()
    db.session.add(p)

    c = Customer.query.filter_by(id=p.customer_id).first()
    c.balance = c.balance - (Decimal.from_float(p.amount) - Decimal.from_float(p.tip))
    db.session.commit()

    return '', 204

