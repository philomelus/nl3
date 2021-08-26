
from datetime import datetime, date, timezone

from flask import request, url_for
from flask_security import login_required

from nl import db, turbo
from nl.customers.popups import bp
from nl.utils import flash_success, PaymentType


@bp.route('/create_payment', methods=('POST',))
@login_required
def create_payment():
    """
    """

    from nl.models.config import Config
    from nl.models.customers import Payment
    
    p = Payment()
    p.customer_id = request.form['payment-cid']
    p.period_id = Config.get('billing-period')
    p.created = p.updated = datetime.now(timezone.utc)
    p.type = request.form['payment-type']
    type = request.form['payment-type']
    if type == PaymentType.CHECK.value:
        p.type = 'CHECK'
    elif type == PaymentType.MONEYORDER.value:
        p.type = 'MONEYORDER'
    elif type == PaymentType.CASH.value:
        p.type = 'CASH'
    else:
        p.type = 'CREDIT'
    p.date = datetime.now(timezone.utc).date()
    p.amount = request.form['payment-amount']
    p.extra1 = request.form['payment-id']
    p.extra2 = ''
    p.tip = float(request.form['payment-tip'])
    p.note = request.form['payment-note']
    db.session.add(p)
    db.session.commit()
    
    return turbo.stream(turbo.append(flash_success(f'Added payment of {p.amount} to'\
                                                   + f' customer {p.customer_id}', True),
                                     target='messages'))

