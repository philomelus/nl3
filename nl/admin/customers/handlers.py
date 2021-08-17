
from flask import render_template
from flask_login import login_required

from nl.admin.customers import bp


@login_required
@bp.route('/billing', methods=('GET', 'POST'))
def billing():
    return render_template('working.html', path='Admin / Customers / Billing')


@login_required
@bp.route('/rates', methods=('GET', 'POST'))
def rates():
    return render_template('working.html', path='Admin / Customers / Rates')


@login_required
@bp.route('/types', methods=('GET', 'POST'))
def types():
    return render_template('working.html', path='Admin / Customers / Types')

