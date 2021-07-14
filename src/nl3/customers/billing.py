
from flask import Blueprint

from nl3.auth import login_required


bp = Blueprint('billing', __name__, url_prefix='/billing')


@login_required
@bp.route('/bill', methods=('GET', 'POST'))
def bill():
    pass


@login_required
@bp.route('/log', methods=('GET', 'POST'))
def log():
    pass

