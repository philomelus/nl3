
from flask_login import login_required

from nl.api.customers import bp


@bp.route('/create', methods=('POST',))
@login_required
def create():
    return '', 204

