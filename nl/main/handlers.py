
from flask import render_template
from flask_login import login_required
from sqlalchemy import or_, and_

from nl import db
from nl.main import bp


@bp.route('/')
@bp.route('/index')
@login_required
def index():
    from nl.models import (Configuration, Customer, CustomerBills,
                           CustomerTypes, Period, Route)
    import calendar
    import datetime
    
    totalCount = Customer.query.filter(or_(Customer.routeList=='Y',
                                           Customer.active=='Y')).count()
    activeAndOwe = Customer.query.filter(and_(Customer.active=='Y',
                                              Customer.balance>0)).count()
    activeAndAhead = Customer.query.filter(and_(or_(Customer.active=='Y',
                                                    Customer.routeList=='Y'),
                                                Customer.balance<=0)).count()
    period = Configuration.get('billing-period')
    title = Period.query.filter_by(id=period).first().title
    lastBills = CustomerBills.query.filter(and_(CustomerBills.export=='Y',
                                                CustomerBills.iid==period)).count()
    

    cpdt = []
    
    types = db.session.query(CustomerTypes.id,
                             CustomerTypes.abbr).filter(CustomerTypes.visible=='Y').all()
    routes = [r[0] for r in db.session.query(Route.id).filter(Route.active=='Y').order_by(Route.title).all()]
    routeTitles = [r[0] for r in db.session.query(Route.title).filter(Route.active=='Y').order_by(Route.title).all()]
    
    for type in types:
        row = [[type[0], type[1]],]
        total = 0
        for route in routes:
            count = Customer.query.filter(and_(Customer.active=='Y',
                                               Customer.type_id==type[0],
                                               Customer.route_id==route)).count()
            total += count
            row.append(count)
        row.append(total)
        cpdt.append(row)

    c = calendar.HTMLCalendar(firstweekday=6)
    calendar = c.formatmonth(datetime.date.today().year,
                             datetime.date.today().month)
    
    vars = {
        'path': 'Home',
        'totalCount': totalCount,
        'activeAndOwe': activeAndOwe,
        'activeAndAhead': activeAndAhead,
        'period': title,
        'lastBills': lastBills,
        'cpdt': cpdt,
        'routes': routeTitles,
        'calendar': calendar
    }
    
    return render_template('index.html', **vars)


@bp.route('/profile')
@login_required
def profile():
    return render_template('working.html', path='Profile')


