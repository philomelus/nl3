
from datetime import datetime, date, timezone

from flask import request, flash, url_for
from flask_security import login_required

from nl import db
from nl.customers.popups import bp
from nl.utils import flash_success


@bp.route('/create_payment', methods=('POST',))
@login_required
def create_payment():
    """
    """

    # p = Payment()
    # p.customer_id = 
    # p.period_id Config.get()
    # p.created = updated = datetime.now(timezone.utc)
    # p.type = form.data['type']
    # p.date = date.now(timezone.utc)
    # p.amount = form.data['amount']
    # p.extra1 = form.data['id']
    # p.extra2 = None
    # p.tip = float(form.data['tip'])
    # p.note = form.data['notes']
    
    
    flash_success(f'Added payment of {p.amount} to customer {p.customer_id}')

    return '', 204

