
from flask import Blueprint

from nl3.auth import login_required
from nl3.admin import customers


bp = Blueprint('admin', __name__, url_prefix='/admin')
bp.register_blueprint(customers.bp)


@login_required
@bp.route('/auditlog', methods=('GET', 'POST'))
def auditlog():
    pass


@login_required
@bp.route('/billing', methods=('GET', 'POST'))
def billing():
    pass


@login_required
@bp.route('/config', methods=('GET', 'POST'))
def config():
    pass


@login_required
@bp.route('/groups', methods=('GET', 'POST'))
def groups():
    pass


@login_required
@bp.route('/periods', methods=('GET', 'POST'))
def periods():
    pass


@login_required
@bp.route('/security', methods=('GET', 'POST'))
def security():
    pass


@login_required
@bp.route('/routes', methods=('GET', 'POST'))
def routes():
    pass


@login_required
@bp.route('/users', methods=('GET', 'POST'))
def users():
    pass

