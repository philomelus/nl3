
from flask import render_template
from flask_login import login_required

from nl.customers import bp


@bp.route('/search', methods=('GET', 'POST'))
@login_required
def search():
    return render_template('working.html', path='Customers / Search')


@bp.route('/flagstops', methods=('GET', 'POST'))
@login_required
def flagstops():
    return render_template('working.html', path='Customers / Flag Stops')


@bp.route('/addnew', methods=('GET', 'POST'))
@login_required
def addnew():
    return render_template('working.html', path='Customers / Add')


@bp.route('/combined', methods=('GET', 'POST'))
@login_required
def combined():
    return render_template('working.html', path='Customers / Combined')

