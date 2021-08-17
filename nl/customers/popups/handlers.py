
from flask import request, flash, url_for
from flask_security import login_required

from nl import db
from nl.customers.popups import bp


@bp.route('/create_payment', methods=('POST',))
@login_required
def create_payment():
    """
    """
    import requests
    
    data = request.get_json()
    ajax = dict(
        customer=data['payment-cid'],
        type=data['payment-type'],
        amount=data['payment-amount'],
        tip=data['payment-tip'],
        notes=data['payment-notes'],
    )
    
    r = requests.post(url_for('api.customers.payments.create', _external=True), ajax)
    r.raise_for_status()
    
    flash_success(f'Added payment of {amount} to customer {customer_id}')

    return '', 204

