
from flask import Blueprint

from nl3.auth import login_required


bp = Blueprint('reports', __name__, url_prefix='/reports')


@login_required
@bp.route('/ahead', methods=('GET', 'POST'))
def ahead():
    pass


@login_required
@bp.route('/behind', methods=('GET', 'POST'))
def behind():
    pass


@login_required
@bp.route('/inactive', methods=('GET', 'POST'))
def inactive():
    pass


@login_required
@bp.route('/orders', methods=('GET', 'POST'))
def orders():
    pass

