
from flask_login import login_required

from nl.customers.reports import bp


@bp.route('/ahead', methods=('GET', 'POST'))
@login_required
def ahead():
    pass


@bp.route('/behind', methods=('GET', 'POST'))
@login_required
def behind():
    pass


@bp.route('/inactive', methods=('GET', 'POST'))
@login_required
def inactive():
    pass


@bp.route('/orders', methods=('GET', 'POST'))
@login_required
def orders():
    pass

