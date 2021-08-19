
from flask import render_template
from flask_security import login_required

from nl.customers.billing import bp


@bp.route('/bill', methods=('GET', 'POST'))
@login_required
def bill():
    return render_template('working.html', path='Customers / Billing / Bill')


@bp.route('/log', methods=('GET', 'POST'))
@login_required
def log():
    return render_template('working.html', path='Customers / Billing / Log')

