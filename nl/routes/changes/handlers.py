
from flask import render_template
from flask_security import login_required

from nl.routes.changes import bp


@bp.route('/notes', methods=('GET', 'POST'))
@login_required
def notes():
    return render_template('working.html', path='Routes / Changes / Notes')


@bp.route('/history', methods=('GET', 'POST'))
@login_required
def history():
    return render_template('working.html', path='Routes / Changes / History')


@bp.route('/report', methods=('GET', 'POST'))
@login_required
def report():
    return render_template('working.html', path='Routes / Changes / Report')

