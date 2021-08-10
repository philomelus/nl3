
from flask import render_template, request, flash, current_app, url_for, redirect, make_response
from flask_login import login_required

from nl import db
from nl.customers import bp
from nl.customers.forms import CreateForm, SearchForm
from nl.utils import (
    customer_type_choices,
    flash_fail,
    flash_success,
    ignore_yes_no,
    pagination,
    PaymentType,
    route_choices,
    state_choices,
    telephone_type_choices,
)

@bp.route('/create', methods=('GET', 'POST'))
@login_required
def create():
    """
    Add new customer form/logic.
    """
    form = CreateForm()
    form.delivery.route.choices = route_choices(False)
    form.delivery.dtype.choices = customer_type_choices(False)

    if form.validate_on_submit():
        from nl.models import (
            Customer,
            CustomerAddresses,
            CustomerNames,
            CustomerServices,
            CustomerTelephones,
            CustomerTypes,
            Route,
            RouteSequences
        )
        from datetime import datetime

        def add_name(customer, field, seq):
            n = CustomerNames()
            n.created = datetime.now()
            n.title = field.title.data
            n.first = field.first.data
            n.last = field.last.data
            n.surname = field.surname.data
            n.sequence = seq
            customer.names.append(n)

        def add_telephone(customer, field, seq):
            t = CustomerTelephones()
            t.created = datetime.now()
            t.type = field.type_.data
            t.number = field.number.data
            t.sequence = seq
            customer.telephones.append(t)

        with db.session.no_autoflush:
            c = Customer()
            c.route_id = form.delivery.route.data
            c.type_id = form.delivery.dtype.data
            c.active = 'Y'
            c.routeList = 'Y'
            c.started = form.delivery.start_date.data
            c.rateType = 'STANDARD'
            c.rateOverride = 0
            c.billType = form.delivery.dtype.data
            c.billBalance = 0
            c.billStopped = 'Y'
            c.billCount = 1
            c.billPeriod = None
            c.billQuantity = 1
            c.billStart = None
            c.billEnd = None
            c.billDue = None
            c.balance = 0
            c.lastPayment = None
            c.billNote = form.notes_.billing.data
            c.notes = form.notes_.notes.data
            c.deliveryNote = form.notes_.delivery.data
            db.session.add(c)

            # Customer names
            add_name(c, form.delivery.name_, CustomerNames.NAM_DELIVERY1)
            name = form.delivery.name2.first.data
            if name:
                add_name(c, form.delivery.name2, CustomerNames.NAM_DELIVERY2)
            name = form.billing.name_.first.data
            if name:
                add_name(c, form.billing.name_, CustomerNames.NAM_BILLING1)
            name = form.billing.name2.first.data
            if name:
                add_name(c, form.billing.name2, CustomerNames.NAM_BILLING2)

            # Customer addresses
            da = CustomerAddresses()
            da.address1 = form.delivery.address.address1.data
            da.address2 = form.delivery.address.address2.data
            da.city = form.delivery.address.city.data
            da.state = form.delivery.address.state.data
            da.zip = form.delivery.address.postal.data
            da.sequence = CustomerAddresses.ADD_DELIVERY
            c.addresses.append(da)

            addr = form.billing.address.address1.data
            if addr:
                ba = CustomerAddresses()
                ba.address1 = form.billing.address.address1.data
                ba.address2 = form.billing.address.address2.data
                ba.city = form.billing.address.city.data
                ba.state = form.billing.address.state.data
                ba.zip = form.billing.address.postal.data
                ba.sequence = CustomerAddresses.ADD_BILLING
                c.addresses.append(ba)

            # Customer telephones
            add_telephone(c, form.delivery.telephone1, CustomerTelephones.TEL_DELIVERY1)
            tele = form.delivery.telephone2.number.data
            if tele:
                add_telephone(c, form.delivery.telephone2, CustomerTelephones.TEL_DELIVERY2)
            tele = form.delivery.telephone3.number.data
            if tele:
                add_telephone(c, form.delivery.telephone3, CustomerTelephones.TEL_DELIVERY3)
            tele = form.billing.telephone1.number.data
            if tele:
                add_telephone(c, form.billing.telephone1, CustomerTelephones.TEL_BILLING1)
            tele = form.billing.telephone2.number.data
            if tele:
                add_telephone(c, form.billing.telephone2, CustomerTelephones.TEL_BILLING2)
            tele = form.billing.telephone3.number.data
            if tele:
                add_telephone(c, form.billing.telephone3, CustomerTelephones.TEL_BILLING3)

            s = RouteSequences()
            s.route_id = c.route_id
            s.order = 99999
            c.sequences.append(s)

            dt = CustomerTypes.query.filter_by(id=c.type_id).first()
            if dt.newChange == 'Y':
                s = CustomerServices()
                s.created = datetime.now()
                s.period_id = None
                s.type = 'START'
                s.when = c.started
                s.why = 'New customer'
                s.ignoreOnBill = 'N'
                s.note = ''
                c.services.append(s)

        db.session.commit()

        flash_success(f'New customer id is {c.id}')

        return redirect(url_for('customers.create'))
        
    return render_template('customers/create.html', path='Customers / Add', form=form)


@bp.route('/css')
def css():
    """
    Return CSS for customer delivery types (set in database).
    """

    from nl.models import CustomerTypes

    types = CustomerTypes.query.all()
    css = '.dt0000 { background-color: white; }\n'
    for t in types:
        css += f'.dt{t.id:04d} {{ background-color: #{t.color:06x}; }}\n'
    response = make_response(css)
    response.headers['Content-Type'] = 'text/css; charset=utf-8'
    return response


@bp.route('/search', methods=('GET', 'POST'))
@login_required
def search():
    """
    Customer search form/logic.
    """
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
            return redirect(url_for('customers.search'))
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
                qry = qry.filter_by(active='N')
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
                if rec:
                    name = rec.first
                    if len(rec.last) > 0:
                        name += ' ' + rec.last
                else:
                    name = '<unknown>'
                customers.append({
                    'type_id': c.type.id,
                    'type': c.type.abbr,
                    'id': c.id,
                    'name': name,
                    'address': c.address().address1,
                    'telephone': c.telephone().number,
                    'route': c.route.title,
                    'balance': c.billBalance
                })
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
                           paginate=pagination(offset=offset, limit=limit, max=count),
                           PaymentType=PaymentType)


