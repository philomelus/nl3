
from datetime import datetime

from flask import request, url_for, redirect
from flask_login import login_required

from nl import db
from nl.api.customers.combined import bp
from nl.models import Customer, CustomerCombinedBills
from nl.utils import flash_success, flash_fail


@bp.route('/add', methods=('POST',))
@login_required
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
@login_required
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
@login_required
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


@bp.route('/update', methods=('POST',))
@login_required
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

