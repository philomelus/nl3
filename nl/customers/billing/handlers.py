

from flask_login import login_required

from nl.customers.billing import bp


@bp.route('/bill', methods=('GET', 'POST'))
@login_required
def bill():
    pass


@bp.route('/log', methods=('GET', 'POST'))
@login_required
def log():
    pass

