
from flask import render_template
from flask_login import login_required

from nl.customers.reports import bp
from nl.customers.reports.forms import *


@bp.route('/ahead', methods=('GET', 'POST'))
@login_required
def ahead():
    form = AheadForm()
    c_one = c_many = ''
    count = form.count.data or 'one'
    if count == 'one':
        c_one = ' checked'
    else:
        c_many = ' checked'
    many = form.many.data or 2
    if form.validate_on_submit():
        from nl.models import (Configuration,
                               Customer,
                               CustomerAddresses,
                               CustomerNames,
                               RouteSequences)
                               
        num = 1
        if count == 'many':
            num = int(form.many.data)

        flagstoptype = Configuration.get('flag-stop-type')
        
        qry = Customer.query
        #qry = qry.join(Customer.addresses.and_(CustomerAddresses.sequence==CustomerAddresses.ADD_DELIVERY))
        #qry = qry.join(Customer.names.and_(CustomerNames.sequence==CustomerNames.NAM_DELIVERY1))
        qry = qry.join(RouteSequences, Customer.id==RouteSequences.tag_id)
        qry = qry.filter(Customer.balance<0)
        qry = qry.filter(Customer.active=='Y')
        qry = qry.filter(Customer.type_id!=flagstoptype)
        qry = qry.order_by(Customer.route_id, RouteSequences.order)
        records = qry.all()

        report = []
        total = 0
        totalall = 0
        count = 0
        for c in records:
            rate = c.rate()
            if c.balance <= -(num * rate):
                n = c.name()
                name = n.first
                if n.last:
                    name += ' ' + n.last
                report.append({
                    'id': c.id,
                    'name': name,
                    'address': c.address().address1,
                    'tid': c.type.id,
                    'type': c.type.abbr,
                    'route': c.route.title,
                    'balance': c.balance,
                    'count': abs(c.balance / rate)
                });
                total += (num * rate)
                totalall += c.balance
                count += 1
        title = f'Customers Ahead {num} Period'
        if num > 1:
            title += 's'
        subtitle = '{} Customers'.format(count)
    else:
        report = []
        title = ''
        subtitle = ''
        total = 0
        totalall = 0

    return render_template('customers/reports/ahead.html',
                           path='Customers / Reports / Ahead',
                           form=form, c_one=c_one, c_many=c_many,
                           report=report, report_title=title,
                           subtitle=subtitle, total=total,
                           totalall=abs(totalall), many=many)


@bp.route('/behind', methods=('GET', 'POST'))
@login_required
def behind():
    form = BehindForm()
    one = ' checked'
    many = ''
    nopmts = ''
    return render_template('customers/reports/behind.html',
                           path='Customers / Reports / Behind',
                           form=form, one=one, many=many, nopmts=nopmts)


@bp.route('/inactive', methods=('GET', 'POST'))
@login_required
def inactive():
    form = InactiveForm()
    
    return render_template('customers/reports/inactive.html',
                           path='Customers / Reports / Inactive',
                           form=form)


@bp.route('/orders', methods=('GET', 'POST'))
@login_required
def orders():
    form = OrdersForm()
    
    return render_template('customers/reports/orders.html',
                           path='Customers / Reports / Orders',
                           form=form)

