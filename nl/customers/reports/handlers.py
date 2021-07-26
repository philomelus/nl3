
from flask import render_template
from flask_login import login_required

from nl.customers.reports import bp
from nl.customers.reports.forms import *


@bp.route('/ahead', methods=('GET', 'POST'))
@login_required
def ahead():
    form = AheadForm()
    count = [' checked', '']
    return render_template('customers/reports/ahead.html',
                           path='Customers / Reports / Ahead',
                           form=form, count=count)


@bp.route('/behind', methods=('GET', 'POST'))
@login_required
def behind():
    form = BehindForm()
    one = ' checked'
    many = ''
    nopmts = ''
    return render_template('customers/reports/behind.html',
                           path='Customers / Reports / Behind',
                           form=form, one=one, many=many, nopmts=nopmts)


@bp.route('/inactive', methods=('GET', 'POST'))
@login_required
def inactive():
    form = InactiveForm()
    
    return render_template('customers/reports/inactive.html',
                           path='Customers / Reports / Inactive',
                           form=form)


@bp.route('/orders', methods=('GET', 'POST'))
@login_required
def orders():
    form = OrdersForm()
    
    return render_template('customers/reports/orders.html',
                           path='Customers / Reports / Orders',
                           form=form)

