
from flask import render_template
from flask_login import login_required

from nl.customers.payments import bp


@bp.route('/addnew', methods=('GET', 'POST'))
@login_required
def addnew():
    return render_template('working.html', path='Customers / Payments / Add')


@bp.route('/search', methods=('GET', 'POST'))
@login_required
def search():
    return render_template('working.html', path='Customers / Payments / Search')

