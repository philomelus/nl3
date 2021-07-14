
from flask import Blueprint

from nl3.auth import login_required


bp = Blueprint('payments', __name__, url_prefix='/payments')


@login_required
@bp.route('/addnew', methods=('GET', 'POST'))
def addnew():
    pass


@login_required
@bp.route('/search', methods=('GET', 'POST'))
def search():
    pass

