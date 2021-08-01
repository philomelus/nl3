
from datetime import datetime

from flask import render_template, request, current_app, url_for, redirect, make_response
from flask_login import login_required

from nl import db
from nl.customers.combined import bp
from nl.customers.combined.forms import CombinedForm
from nl.models import Customer, CustomerCombinedBills
from nl.utils import flash_success, flash_fail


@bp.route('/add', methods=('POST',))
def add():
    id1 = request.form['add-id1']
    id2 = request.form['add-id2']
    record = CustomerCombinedBills()
    record.customer_id_main = id1
    record.customer_id_secondary = id2
    record.created = datetime.today()
    db.session.add(record)
    db.session.commit()
    flash_success(f'Added additional combined customer successfully.')
    return redirect(url_for('customers.combined.index'))


@bp.route('/create', methods=('POST',))
def create():
    id1 = request.form['create-id1']
    id2 = request.form['create-id2']
    record = CustomerCombinedBills()
    record.customer_id_main = id1
    record.customer_id_secondary = id2
    record.created = datetime.today()
    db.session.add(record)
    db.session.commit()
    flash_success(f'Created new combined customer successfully.')
    return redirect(url_for('customers.combined.index'))


@bp.route('/delete', methods=('POST',))
def delete():
    id1 = request.form['delete-id1']
    id2 = request.form['delete-id2']
    record = CustomerCombinedBills.query.filter(CustomerCombinedBills.customer_id_main==id1,
                                                CustomerCombinedBills.customer_id_secondary==id2).first()
    if record:
        db.session.delete(record)
        db.session.commit()
        flash_success('Combined customer deleted successfully.')
    else:
        flash_fail('Unable to delete combined customers.')
    return redirect(url_for('customers.combined.index'))


@bp.route('/', methods=('GET', 'POST'))
@bp.route('/index', methods=('GET', 'POST'))
@login_required
def index():
    """
    Customer Combined bills form/logic.
    """
    form = CombinedForm
    from nl.models import CustomerAddresses, CustomerNames
    from sqlalchemy import asc, select, distinct
 
    primary = db.session.execute(select(distinct(CustomerCombinedBills.customer_id_main))).scalars().all()
    
    combined = []
    for p in primary:
        info = Customer.query.filter_by(id=p).first()
        others =[]

        secondaries = db.session.execute(select(distinct(CustomerCombinedBills.customer_id_secondary))
                                         .filter(CustomerCombinedBills.customer_id_main==p)).scalars().all()
        for s in secondaries:
            oi = Customer.query.filter_by(id=s).first()
            n = oi.name()
            name = n.first
            if n.last:
                name += ' ' + n.last
            others.append({
                'id': oi.id,
                'name': name,
                'address': oi.address().address1
            })

        n = info.name()
        name = n.first
        if n.last:
            name += ' ' + n.last
        combined.append({
            'id': info.id,
            'name': name,
            'address': info.address().address1,
            'count': len(others),
            'others': others
        })
    count = len(combined)

    return render_template('customers/combined/index.html', path='Customers / Combined',
                           form=form, count=count, combined=combined)


@bp.route('/update', methods=('POST',))
def update():
    id1 = request.form['update-id1']
    id2 = request.form['update-id2-original']
    idnew = request.form['update-id2']
    record = CustomerCombinedBills.query.filter(CustomerCombinedBills.customer_id_main==id1,
                                                CustomerCombinedBills.customer_id_secondary==id2).first()
    record.customer_id_secondary = idnew
    db.session.commit()
    flash_success('Combined update successful.')
    return redirect(url_for('customers.combined.index'))

