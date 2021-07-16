
from flask_login import login_required

from nl.admin.customers import bp


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

