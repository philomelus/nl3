
from decimal import Decimal
from datetime import date, datetime

from flask import redirect, render_template, url_for
from flask_login import login_required

from nl import db
from nl.utils import flash_success, MoneyOps, pagination, PaymentType, period_choices
from nl.customers.payments import bp
from nl.customers.payments.forms import AddNewForm, SearchForm


@bp.route('/addnew', methods=('GET', 'POST'))
@login_required
def addnew():
    form = AddNewForm()
    if form.validate_on_submit():
        from nl.models import Configuration, Customer, CustomerPayments

        p = CustomerPayments()
        p.customer_id = int(form.customer.data)
        p.created = datetime.today()
        type_ = form.type_.data
        if type_ == PaymentType.CHECK.value:
            p.type = 'CHECK'
            p.extra1 = form.id_.data
            p.extra2 = ''
        elif type_ == PaymentType.MONEYORDER.value:
            p.type = 'MONEYORDER'
            p.extra1 = form.id_.data
            p.extra2 = ''
        elif type_ == PaymentType.CASH.value:
            p.type = 'CASH'
            p.extra1 = p.extra2 = ''
        elif type_ == PaymentType.CREDIT.value:
            p.type = 'CREDIT'
            p.extra1 = p.extra2 = ''
        p.amount = form.amount.data
        tip = form.tip.data
        if tip is None:
            tip = 0
        p.tip = tip
        p.notes = form.notes.data
        p.period_id = Configuration.get('billing-period')
        p.date = date.today()
        db.session.add(p)

        c = Customer.query.filter_by(id=p.customer_id).first()
        c.balance = c.balance - Decimal.from_float(p.amount) - Decimal.from_float(p.tip)
        db.session.commit()

        flash_success(f'Added payment of {p.amount} to customer {p.customer_id}')

        return redirect(url_for('customers.payments.addnew'))
    
    return render_template('customers/payments/addnew.html', path='Customers / Payments / Add',
                           form=form)


@bp.route('/search', methods=('GET', 'POST'))
@login_required
def search():
    form = SearchForm()
    form.period.choices = period_choices(any=True)
    
    doResults = False
    if form.validate_on_submit():
        limit = form.limit.data or 10
        offset = form.offset.data or 0
        action = form.action.data
        
        if action == 'clear':
            return redirect(url_for('customers.payments.search'))
        elif action == 'begin':
            form.offset.data = offset = 0
            doResults = True
        elif action == 'prev':
            offset -= limit
            if offset < 0:
                offset = 0
            form.offset.data = offset
            doResults = True
        else: # refresh
            doResults = True

        if doResults:
            from nl.models import (Customer,
                                   CustomerAddresses,
                                   CustomerNames,
                                   CustomerPayments,
                                   CustomerTelephones,
                                   CustomerTypes,
                                   Route)
            from sqlalchemy import and_, or_, func
        
            qry = CustomerPayments.query

            # customer id
            customer = form.customer.data
            if customer:
                qry = qry.filter_by(customer_id=int(customer))
            
            # amount
            amount = form.amount.data
            if amount:
                op = int(form.amount_op.data)
                if op == MoneyOps.GREATER_EQUAL.value:
                    qry = qry.filter(CustomerPayments.amount>=amount)
                elif op == MoneyOps.GREATER.value:
                    qry = qry.filter(CustomerPayments.amount>amount)
                elif op == MoneyOps.EQUAL.value:
                    qry = qry.filter(CustomerPayments.amount==amount)
                elif op == MoneyOps.LESS.value:
                    qry = qry.filter(CustomerPayments.amount<amount)
                else:  #op == MoneyOps.LESS_EQUAL.value
                    qry = qry.filter(CustomerPayments.amount<=amount)
            
            # tip
            # TODO: BUGBUG: 0 doesn't work as value for tip.  Not sure how
            #       to fix, as its a float already when we get it and checking
            #       for 0 specifically works even when field is empty.
            #       Probably have to not coerce to float in order to implement
            #       correctly.  Do any users actually care ... ???
            tip = form.tip.data
            if tip:
                op = int(form.tip_op.data)
                if op == MoneyOps.GREATER_EQUAL.value:
                    qry = qry.filter(CustomerPayments.tip>=tip)
                elif op == MoneyOps.GREATER.value:
                    qry = qry.filter(CustomerPayments.tip>tip)
                elif op == MoneyOps.EQUAL.value:
                    qry = qry.filter(CustomerPayments.tip==tip)
                elif op == MoneyOps.LESS.value:
                    qry = qry.filter(CustomerPayments.tip<tip)
                else:  #op == MoneyOps.LESS_EQUAL.value
                    qry = qry.filter(CustomerPayments.tip<=tip)
            
            # payment id
            id_ = form.payment.data
            if id_:
                qry = qry.filter_by(id=id_)
            
            # id_
            id_ = form.id_.data
            if (id_):
                ids = ['%'+i.upper()+'%' for i in id_.split(' ')]
                cond = [func.upper(CustomerPayments.extra1).like(iuc) for iuc in ids]
                qry = qry.filter(or_(*cond))
            
            # notes
            notes = form.notes.data
            if notes:
                words = ['%'+w.upper()+'%' for w in notes.split(' ')]
                cond = [func.upper(CustomerPayments.note).like(wuc) for wuc in words]
                qry = qry.filter(and_(*cond))
            
            # period
            period = int(form.period.data)
            if period and period > 0:
                qry = qry.filter_by(period_id=period)
            
            # payment type
            type_ = int(form.type_.data)
            if type_ and type_ != 99:
                if type_ == PaymentType.CHECK.value:
                    qry = qry.filter_by(type='CHECK')
                elif type_ == PaymentType.MONEYORDER.value:
                    qry = qry.filter_by(type='MONEYORDER')
                elif type_ == PaymentType.CASH.value:
                    qry = qry.filter_by(type='CASH')
                else: # type_ == PaymentType.CREDIT.value:
                    qry = qry.filter_by(type='CREDIT')
                    
            # after date/time
            after = form.after.data
            if after:
                qry = qry.filter(CustomerPayments.created>after)
                
            # before date/time
            before = form.before.data
            if before:
                qry = qry.filter(CustomerPayments.created<before)
            
            # Get total matching records
            count = qry.count()

            # Handle advancement requests
            if action == 'next':
                offset += limit
                if offset > count:
                    offset = count - limit
                form.offset.data = offset
            elif action == 'end':
                offset = count - limit
                form.offset.data = offset

            # Get the data and message into form data
            records = qry.order_by(CustomerPayments.id).limit(limit).offset(offset).all()
            payments = []
            for p in records:
                payments.append({
                    'payment_id': p.id,
                    'customer_id': p.customer_id,
                    'when': p.date,
                    'period_id': p.period_id,
                    'type': p.type,
                    'id': p.extra1,
                    'amount': p.amount,
                    'tip': p.tip
                })
        else:
            count = 0
            payments = []
    else:
        offset = 0
        limit = 10
        count = 0
        payments = []
    
    return render_template('customers/payments/search.html', path='Customers / Payments / Search',
                           form=form, paginate=pagination(offset=offset, limit=limit, max=count),
                           doResults=doResults, payments=payments, count=count)

