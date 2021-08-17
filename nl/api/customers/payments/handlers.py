
from flask import jsonify, request
from flask_login import login_required
from werkzeug.http import HTTP_STATUS_CODES

from nl import db

from nl.api.customers.payments import bp


def error_response(status_code, message=None):
    payload = {'error': HTTP_STATUS_CODES.get(status_code, 'Unknown error')}
    if message:
        payload['message'] = message
    response = jsonify(payload)
    response.status_code = status_code
    return response


def bad_request(message):
    return error_response(400, message)


@bp.route('/create', methods=('POST',))
@login_required
def create():
    from nl.models.config import Config
    from nl.models.customers import Customer, Payment

    # Validate parameters
    data = request.get_json() or {}
    if 'customer' not in data:
        return bad_request('must include customer id')
    customer = int(data['customer'])
    c = Customer.query.filter_by(id=customer).first()
    if c == None:
        return bad_request('unable to locate customer')
    if 'type' not in data:
        return bad_request('must include payment type')
    type_ = int(data['type'])
    if type_ not in (PaymentType.CHECK.value,
                    PaymentType.MONEYORDER.value,
                    PaymentType.CASH.value,
                    PaymentType.CREDIT.value):
        return bad_request('unknown payment type')
    if 'amount' not in data:
        return bad_request('must include payment amount')

    # Add payment
    p = Payment(customer_id=c.id)
    p.created = p.updated = datetime.utcnow()
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
    p.amount = float(data['amount'])
    p.tip = float(data.get('tip', 0))
    p.notes = data['notes']
    p.period_id = Config.get('billing-period')
    p.date = datetime.now().date()
    db.session.add(p)

    # Update customer balance
    c.balance = c.balance - (Decimal.from_float(p.amount) - Decimal.from_float(p.tip))
    db.session.commit()

    return dict(result=True, id=p.id), 200

