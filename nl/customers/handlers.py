
from flask import render_template, request, flash, current_app
from flask_login import login_required

from nl.customers import bp
from nl.customers.forms import SearchForm
from nl.utils import (pagination, route_choices, customer_type_choices, flash_success,
                      flash_fail, ignore_yes_no)


@bp.route('/search', methods=('GET', 'POST'))
@login_required
def search():
    # Create/setup form
    form = SearchForm()
    form.route.choices = route_choices()
    form.dtype.choices = customer_type_choices()
    form.routeList.choices = ignore_yes_no
    form.billing.choices = ignore_yes_no
    
    # Handle submit
    args = {}
    doResults = False
    if form.validate_on_submit():
        from nl.models import Customer, CustomerAddresses, CustomerNames, CustomerTelephones, CustomerTypes, Route
        from sqlalchemy import and_, or_, func, text
        
        limit = form.limit.data or 10
        offset = form.offset.data or 0
        action = form.action.data
        if action == 'clear':
            form.customer.data = None
            form.route.data = '0'
            form.dtype.data = '0'
            form.routeList.data = '0'
            form.billing.data = '0'
            form.name.data = None
            form.address.data = None
            form.postal.data = None
            form.telephone.data = None
            form.offset.data = 0
            offset = 0
        elif action == 'prev':
            offset -= limit
            if offset < 0:
                offset = 0
            form.offset.data = offset
            doResults = True
        elif action == 'begin':
            form.offset.data = 0
            offset = 0
            doResults = True
        else:
            doResults = True

        if doResults:
            # Build query
            qry = Customer.query.distinct(Customer.id)
            filters = {}

            # customer id
            customer = form.customer.data
            if customer:
                qry = qry.filter_by(id=int(customer))
        
            # route
            route = form.route.data
            if route:
                route = int(route)
                if route > 0:
                    qry = qry.filter_by(route_id=int(route))
        
            # type
            dtype = form.dtype.data
            if dtype:
                dtype = int(dtype)
                if dtype > 0:
                    qry = qry.filter_by(type_id=int(dtype))

            # routeList
            routeList = form.routeList.data
            if routeList == '2':
                qry = qry.filter_by(routeList='N')
            elif routeList == '1':
                qry = qry.filter_by(routeList='Y')

            # billing
            active = form.billing.data
            if active == '2':
                qry = qry.filter_by(active='Y')
            elif active == '1':
                qry = qry.filter_by(active='Y')

            # name
            name = form.name.data
            if name:
                names = ['%'+n.upper()+'%' for n in name.split(' ')]
                cond = [func.upper(CustomerNames.first).like(nuc) for nuc in names]
                cond += [func.upper(CustomerNames.last).like(nuc) for nuc in names]
                qry = qry.join(Customer.names.and_(or_(*cond)))

            # address and postal
            address = form.address.data
            postal = form.postal.data
            if address or postal:
                cond1 = None
                if address:
                    addrs = ['%'+a.upper()+'%' for a in address.split(' ')]
                    cond1 = [func.upper(CustomerAddresses.address1).like(addr) for addr in addrs]
                    cond1 += [func.upper(CustomerAddresses.address2).like(addr) for addr in addrs]
                cond2 = None
                if postal:
                    posts = ['%'+p.upper()+'%' for p in postal.split(' ')]
                    cond2 = [func.upper(CustomerAddresses.zip).like(post) for post in posts]
                if cond1:
                    if cond2:
                        qry = qry.join(Customer.addresses.and_(or_(*cond1), or_(*cond2)))
                    else:
                        qry = qry.join(Customer.addresses.and_(or_(*cond1)))
                elif cond2:
                    qry = qry.join(Customer.addresses.and_(or_(*cond2)))

            # telephone
            telephone = form.telephone.data
            if telephone:
                teles = ['%'+t+'%' for t in telephone.split(' ')]
                cond = [CustomerTelephones.number.like(tele) for tele in teles]
                qry = qry.join(Customer.telephones.and_(or_(*cond)))

            # Get the number of matching records
            count = qry.count()

            # If advancement requested, do it now that we know number of records
            if action == 'next':
                offset += limit
                if offset > count:
                    offset = count - limit
                form.offset.data = offset
            elif action == 'end':
                offset = count - limit
                form.offset.data = offset

            # Get the actual data and massage into form data
            records = qry.limit(limit).offset(offset).all()
            customers = []
            for c in records:
                rec = c.name()
                name = rec.first
                if len(rec.last) > 0:
                    name += ' ' + rec.last
                customers.append({
                    'type': c.type.abbr,
                    'id': c.id,
                    'name': name,
                    'address': c.address().address1,
                    'telephone': c.telephone().number,
                    'route': c.route.title,
                    'balance': c.billBalance
                    })
            doResults = True
        else:
            count = 0
            customers = []
    else:
        count = 0
        offset = 0
        limit = 10
        customers = []
    return render_template('customers/search.html', path='Customers / Search', form=form,
                           doResults=doResults, count=count, customers=customers,
                           paginate=pagination(offset=offset, limit=limit, max=count))


@bp.route('/flagstops', methods=('GET', 'POST'))
@login_required
def flagstops():
    return render_template('working.html', path='Customers / Flag Stops')


@bp.route('/addnew', methods=('GET', 'POST'))
@login_required
def addnew():
    return render_template('working.html', path='Customers / Add')


@bp.route('/combined', methods=('GET', 'POST'))
@login_required
def combined():
    return render_template('working.html', path='Customers / Combined')

