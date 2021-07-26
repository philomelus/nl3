
from flask import render_template
from flask_login import login_required

from nl.utils import pagination, period_choices
from nl.customers.payments import bp
from nl.customers.payments.forms import AddNewForm, SearchForm


@bp.route('/addnew', methods=('GET', 'POST'))
@login_required
def addnew():
    form = AddNewForm()
    
    return render_template('customers/payments/addnew.html', path='Customers / Payments / Add',
                           form=form)


@bp.route('/search', methods=('GET', 'POST'))
@login_required
def search():
    form = SearchForm()
    form.period.choices = period_choices()

    offset = 0
    limit = 10
    count = 0
    
    return render_template('customers/payments/search.html', path='Customers / Payments / Search',
                           form=form, paginate=pagination(offset=offset, limit=limit, max=count))

