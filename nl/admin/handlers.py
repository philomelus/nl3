
from flask_login import login_required

from nl.admin import bp


@bp.route('/auditlog', methods=('GET', 'POST'))
@login_required
def auditlog():
    pass


@bp.route('/billing', methods=('GET', 'POST'))
@login_required
def billing():
    pass


@bp.route('/config', methods=('GET', 'POST'))
@login_required
def config():
    pass


@bp.route('/groups', methods=('GET', 'POST'))
@login_required
def groups():
    pass


@bp.route('/periods', methods=('GET', 'POST'))
@login_required
def periods():
    pass


@bp.route('/security', methods=('GET', 'POST'))
@login_required
def security():
    pass


@bp.route('/routes', methods=('GET', 'POST'))
@login_required
def routes():
    pass


@bp.route('/users', methods=('GET', 'POST'))
@login_required
def users():
    pass

