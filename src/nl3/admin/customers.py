
from flask import Blueprint
from nl3.auth import login_required


bp = Blueprint('customers', __name__, url_prefix='/customers')


@login_required
@bp.route('/billing', methods=('GET', 'POST'))
def billing():
    pass


@login_required
@bp.route('/rates', methods=('GET', 'POST'))
def rates():
    pass


@login_required
@bp.route('/types', methods=('GET', 'POST'))
def types():
    pass

