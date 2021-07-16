

from flask_login import login_required

from nl.customers.payments import bp


@bp.route('/addnew', methods=('GET', 'POST'))
@login_required
def addnew():
    pass


@bp.route('/search', methods=('GET', 'POST'))
@login_required
def search():
    pass

