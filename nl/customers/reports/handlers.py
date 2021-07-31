
from flask import render_template
from flask_login import login_required

from nl.customers.reports import bp
from nl.customers.reports.forms import *

__all__ = [
    'ahead',
    'behind',
    'inactive',
    'orders'
]

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
        
        qry = Customer.query\
                      .join(RouteSequences, Customer.id==RouteSequences.tag_id)\
                      .filter(Customer.balance<0)\
                      .filter(Customer.active=='Y')\
                      .filter(Customer.type_id!=flagstoptype)\
                      .order_by(Customer.route_id, RouteSequences.order)
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
    what = form.what.data
    if what:
        one = many = nopmts = ''
        if what == 'one':
            one = ' checked'
        elif what == 'many':
            many = ' checked'
        elif what == 'nopmts':
            nopmts = ' checked'
    else:
        one = ' checked'
        many = nopmts = ''
    
    vars = {
        'form': form,
        'one': one,
        'many': many,
        'nopmts': nopmts
    }
    if form.validate_on_submit():
        if what == 'one':
            return behind_one(vars)
        elif what == 'many':
            return behind_many(vars)
        elif what == 'nopmts':
            return behind_nopmts(vars)
        
    return render_template('customers/reports/behind.html',
                           path='Customers / Reports / Behind', **vars)


def behind_one(vars):
    from nl.models import Customer, RouteSequences

    from flask import current_app
    current_app.logger.debug('behind_one called')
    
    qry = Customer.query\
                  .join(RouteSequences, Customer.id==RouteSequences.tag_id)\
                  .filter(Customer.balance<0)\
                  .filter(Customer.active=='Y')\
                  .order_by(Customer.route_id, RouteSequences.order)
    records = qry.all()

    report = []
    count = 0
    for c in records:
        rate = c.rate()
        diff = c.balance - (2 * rate)
        if diff >= 0 and diff <= rate:
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
            });
            count += 1
    title = f'Customers Behind 1 Period'
    subtitle = '{} Customer'.format(count)
    if count == 0 or count > 1:
        subtitle += 's'

    return render_template('customers/reports/behind.html',
                           path='Customers / Reports / Behind', **vars,
                           report_title=title, subtitle=subtitle, report=report,
                           count=count, doReport=True)


def behind_many(vars):
    from nl.models import Customer, RouteSequences

    from flask import current_app
    current_app.logger.debug('behind_one called')
    
    qry = Customer.query\
                  .join(RouteSequences, Customer.id==RouteSequences.tag_id)\
                  .filter(Customer.balance<0)\
                  .filter(Customer.active=='Y')\
                  .order_by(Customer.route_id, RouteSequences.order)
    records = qry.all()

    report = []
    count = 0
    for c in records:
        rate = c.rate()
        diff = c.balance - (3 * rate)
        if diff >= 0:
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
            });
            count += 1
    title = f'Customers Behind More Than 1 Period'
    subtitle = '{} Customer'.format(count)
    if count == 0 or count > 1:
        subtitle += 's'

    return render_template('customers/reports/behind.html',
                           path='Customers / Reports / Behind', **vars,
                           report_title=title, subtitle=subtitle, report=report,
                           count=count, doReport=True)


def behind_nopmts(vars):
    from nl.models import Customer, RouteSequences

    from flask import current_app
    current_app.logger.debug('behind_one called')

    sq = Customer.query.join(Customer.payments)
    qry = Customer.query\
                  .join(RouteSequences, Customer.id==RouteSequences.tag_id)\
                  .filter(Customer.balance<0)\
                  .filter(Customer.active=='Y')\
                  .order_by(Customer.route_id, RouteSequences.order)
    records = qry.all()

    report = []
    count = 0
    for c in records:
        rate = c.rate()
        diff = c.balance - (3 * rate)
        if diff >= 0 and len(c.payments) == 0:
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
            });
            count += 1
    title = f'Customers Behind  1 Period With No Payments'
    subtitle = '{} Customer'.format(count)
    if count == 0 or count > 1:
        subtitle += 's'

    return render_template('customers/reports/behind.html',
                           path='Customers / Reports / Behind', **vars,
                           report_title=title, subtitle=subtitle, report=report,
                           count=count, doReport=True)


@bp.route('/inactive', methods=('GET', 'POST'))
@login_required
def inactive():
    form = InactiveForm()
    doReport = False
    
    if form.validate_on_submit():
        from nl.models import Customer, RouteSequences

        routeList = form.routeList.data

        qry = Customer.query\
                      .join(RouteSequences, Customer.id==RouteSequences.tag_id)\
                      .filter(Customer.active=='N')
        if routeList == '1':
            qry = qry.filter(Customer.routeList=='Y')
        elif routeList == '2':
            qry = qry.filter(Customer.routeList=='N')
        records = qry.order_by(Customer.route_id, RouteSequences.order).all()

        report = []
        count = 0
        for c in records:
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
            });
            count += 1
        subtitle = '{} Customer'.format(count)
        if count == 0 or count > 1:
            subtitle += 's'
        doReport = True
    else:
        routeList = '1'
        report = []
        subtitle = ''
        count = 0

    return render_template('customers/reports/inactive.html',
                           path='Customers / Reports / Inactive',
                           form=form, routeList=routeList, subtitle=subtitle, report=report,
                           doReport=doReport, count=count)

