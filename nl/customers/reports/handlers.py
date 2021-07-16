
from flask import render_template
from flask_login import login_required

from nl.customers.reports import bp


@bp.route('/ahead', methods=('GET', 'POST'))
@login_required
def ahead():
    return render_template('working.html', path='Customers / Reports / Ahead')


@bp.route('/behind', methods=('GET', 'POST'))
@login_required
def behind():
    return render_template('working.html', path='Customers / Reports / Behind')


@bp.route('/inactive', methods=('GET', 'POST'))
@login_required
def inactive():
    return render_template('working.html', path='Customers / Reports / Inactive')


@bp.route('/orders', methods=('GET', 'POST'))
@login_required
def orders():
    return render_template('working.html', path='Customers / Reports / Orders')

