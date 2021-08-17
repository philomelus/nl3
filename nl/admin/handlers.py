
from flask import render_template
from flask_login import login_required

from nl.admin import bp


@bp.route('/auditlog', methods=('GET', 'POST'))
@login_required
def auditlog():
    return render_template('working.html', path='Admin / Audit Log')


@bp.route('/billing', methods=('GET', 'POST'))
@login_required
def billing():
    return render_template('working.html', path='Admin / Billing')


@bp.route('/config', methods=('GET', 'POST'))
@login_required
def config():
    return render_template('working.html', path='Admin / Configuration')


@bp.route('/groups', methods=('GET', 'POST'))
@login_required
def groups():
    return render_template('working.html', path='Admin / Groups')


@bp.route('/periods', methods=('GET', 'POST'))
@login_required
def periods():
    return render_template('working.html', path='Admin / Periods')


@bp.route('/security', methods=('GET', 'POST'))
@login_required
def security():
    return render_template('working.html', path='Admin / Security')


@bp.route('/routes', methods=('GET', 'POST'))
@login_required
def routes():
    return render_template('working.html', path='Admin / Routes')


@bp.route('/users', methods=('GET', 'POST'))
@login_required
def users():
    return render_template('working.html', path='Admin / Users')

