
from datetime import datetime, date, timezone

from flask import request, url_for
from flask_security import login_required

from nl import db, turbo
from nl.customers.popups import bp
from nl.utils import (
    ComplaintResult,
    ComplaintType,
    flash_success,
    PaymentType,
)


@bp.route('/adjustment', methods=('POST',))
@login_required
def adjustment():
    """
    Add adjustment for customer.
    """
    from nl.models.customers import Adjustment

    a = Adjustment()
    #db.session.add(a)
    #db.session.commit()
    
    return turbo.stream(turbo.append(flash_success(f'Added adjustment for customer c.customer_id',
                                                   True), target='messages'))


@bp.route('/complaint', methods=('POST',))
@login_required
def complaint():
    """
    Add delivery service complaint for customer.
    """

    from nl.models.customers import Complaint

    c = Complaint()
    c.customer_id = int(request.form['complaint-cid'])
    c.period_id = None
    c.created = c.updated = datetime.now(timezone.utc)
    c.type = ComplaintType.to_db(int(request.form['complaint-what']))
    c.result = ComplaintResult.to_db(int(request.form['complaint-result']))
    c.when = date.fromisoformat(request.form['complaint-when'])
    why = request.form['complaint-why']
    if why == None:
        why = ''
    c.why = why
    note = request.form['complaint-note']
    if note == None:
        note = ''
    c.note = note
    c.amount = 0
    c.ignoreOnBill = 'N'
    db.session.add(c)
    db.session.commit()
    
    return turbo.stream(turbo.append(flash_success(f'Added complaint for customer {c.customer_id}',
                                                   True), target='messages'))


@bp.route('/create_payment', methods=('POST',))
@login_required
def create_payment():
    """
    Add payment for customer.
    """

    from nl.models.config import Config
    from nl.models.customers import Payment
    
    p = Payment()
    p.customer_id = request.form['payment-cid']
    p.period_id = Config.get('billing-period')
    p.created = p.updated = datetime.now(timezone.utc)
    p.type = request.form['payment-type']
    type = request.form['payment-type']
    if type == PaymentType.CHECK.value:
        p.type = 'CHECK'
    elif type == PaymentType.MONEYORDER.value:
        p.type = 'MONEYORDER'
    elif type == PaymentType.CASH.value:
        p.type = 'CASH'
    else:
        p.type = 'CREDIT'
    p.date = datetime.now(timezone.utc).date()
    p.amount = request.form['payment-amount']
    p.extra1 = request.form['payment-id']
    p.extra2 = ''
    p.tip = float(request.form['payment-tip'])
    p.note = request.form['payment-note']
    db.session.add(p)
    db.session.commit()
    
    return turbo.stream(turbo.append(flash_success(f'Added payment of {p.amount} to'\
                                                   + f' customer {p.customer_id}', True),
                                     target='messages'))


@bp.route('/service', methods=('POST',))
@login_required
def service():
    """
    Add start/stop service for customer.
    """

    from nl.models.customers import ServiceChange

    def add_change(cust_id, type, when, why, notes):
        sc = ServiceChange()
        sc.customer_id = cust_id
        sc.period_id = None
        sc.created = sc.updated = datetime.now(timezone.utc)
        sc.type = type
        sc.when = when
        sc.why = why
        sc.notes = notes
        sc.ignoreOnBill = 'N'
        db.session.add(sc)
        return sc

    cust_id = int(request.form['service-cid'])
    why = request.form['service-why']
    if why == None or len(why) == 0:
        why = 'Customer Request'
    notes = request.form['service-notes']
    if notes == None:
        notes = ''

    stop = None
    if 'service-addstop' in request.form:
        when = date.fromisoformat(request.form['service-stopdate'])
        stop = add_change(cust_id, 'STOP', when, why, notes)

    start = None
    if 'service-addstart' in request.form:
        when = date.fromisoformat(request.form['service-startdate'])
        start = add_change(cust_id, 'START', when, why, notes)

    db.session.commit()
    
    if stop != None:
        if start != None:
            return turbo.stream([
                turbo.append(flash_success(f'Added stop on {stop.when}'\
                                           + f' to customer {cust_id}.', True),
                             target='messages'),
                turbo.append(flash_success(f'Added start on {start.when}'\
                                           + f' to customer {cust_id}.', True),
                             target='messages')
            ])
        else:
            return turbo.stream(turbo.append(flash_success(f'Added stop on {stop.when}'\
                                                           + f' to customer {cust_id}.', True),
                                             target='messages'))
    else:
        return turbo.stream(turbo.append(flash_success(f'Added start on {start.when}'\
                                                       + f' to customer {cust_id}.', True),
                                         target='messages'))

    
@bp.route('/type', methods=('POST',))
@login_required
def type():
    """
    Change customer delivery type.
    """
    from nl.models.customers import ServiceType

    st = ServiceType()
    st.customer_id = int(request.form['type-cid'])
    st.period_id = None
    st.created = st.updated = datetime.now(timezone.utc)
    st.when = date.fromisoformat(request.form['type-when'])
    st.type_id_from = int(request.form['type-tid'])
    st.type_id_to = int(request.form['type-to'])
    why = request.form['type-why']
    if why == None or len(why) == 0:
        why = 'Customer Request'
    st.why = why
    note = request.form['type-note']
    if note == None:
        note = ''
    st.note = note
    st.ignoreOnBill = 'N'
    db.session.add(st)
    db.session.commit()
        
    return turbo.stream(turbo.append(flash_success(f'Changed customer {st.customer_id}\'s delivery type.',
                                                   True), target='messages'))


@bp.route('/update', methods=('POST',))
@login_required
def update():
    """
    Modify customer data.
    """
    return turbo.stream(turbo.append(flash_success(f'Updated customer customer_id\'s record.',
                                                   True), target='messages'))

