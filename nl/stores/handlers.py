
from flask_login import login_required

from nl.stores import bp


@bp.route('/', methods=('GET', 'POST'))
@bp.route('/index', methods=('GET', 'POST'))
@login_required
def index():
    pass

