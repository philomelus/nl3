<div id="menu">
    <div class="w3-bar w3-black">
{if allowed('page', S_HOME)}
        <a href="{$ROOT}index.php" class="w3-bar-item w3-button">Home</a>
{/if}
{if allowed('page', S_CUSTOMERS)}
        <div class="w3-dropdown-hover">
            <button class="w3-button">Customers</button>
            <div class="w3-dropdown-content w3-bar-block w3-card-4">
    {if allowed('page', SC_SEARCH)}
                <a href="{$ROOT}customers/search.php" class="w3-bar-item w3-button">Search</a>
    {/if}
    {if allowed('page', SC_FLAGSTOPS)}
                <a href="{$ROOT}customers/flagstops.php" class="w3-bar-item w3-button">Flag Stops</a>
    {/if}
    {if allowed('page', SC_ADDNEW)}
                <a href="{$ROOT}customers/addnew.php" class="w3-bar-item w3-button">Add</a>
    {/if}
    {if allowed('page', SC_COMBINED)}
                <a href="{$ROOT}customers/combined.php" class="w3-bar-item w3-button">Combined</a>
    {/if}
                <hr class="w3-separator">
    {if allowed('page', SC_PAYMENTS)}
                <div class="w3-dropdown-hover">
                    <button class="w3-button">Payments</button>
                    <div class="w3-dropdown-content w3-bar-block w3-card-4">
                        <a href="{$ROOT}customers/payments/addnew.php" class="w3-bar-item w3-button">Add</a>
                        <a href="{$ROOT}customers/payments/search.php" class="w3-bar-item w3-button">Search</a>
                    </div>
                </div>
    {/if}
    {if allowed('page', SC_REPORTS)}
                <div class="w3-dropdown-hover">
                    <button class="w3-button">Reports</button>
                    <div class="w3-dropdown-content w3-bar-block w3-card-4">
                        <a href="{$ROOT}customers/reports/ahead.php" class="w3-bar-item w3-button">Ahead</a>
                        <a href="{$ROOT}customers/reports/behind.php" class="w3-bar-item w3-button">Behind</a>
                        <a href="{$ROOT}customers/reports/inactive.php" class="w3-bar-item w3-button">Inactive</a>
                        <a href="{$ROOT}customers/reports/orders.php" class="w3-bar-item w3-button">Orders</a>
                    </div>
                </div>
    {/if}
    {if allowed('page', SC_BILLING)}
                <div class="w3-dropdown-hover">
                    <button class="w3-button">Billing</button>
                    <div class="w3-dropdown-content w3-bar-block w3-card-4">
                        <a href="{$ROOT}customers/billing/bill.php" class="w3-bar-item w3-button">Bill</a>
                        <a href="{$ROOT}customers/billing/log.php" class="w3-bar-item w3-button">Log</a>
                    </div>
                </div>
    {/if}
            </div>
        </div>
{/if}
{if allowed('page', S_STORES)}
        <a href="stores.php" class="w3-bar-item w3-button">Stores &amp; Racks</a>
{/if}
{if allowed('page', S_ROUTE)}
        <div class="w3-dropdown-hover">
            <button class="w3-button">Routes</button>
            <div class="w3-dropdown-content w3-bar-block w3-card-4">
    {if allowed('page', SE_SEQUENCING)}
                <a href="{$ROOT}routes/sequencing.php" class="w3-bar-item w3-button">Sequencing</a>
    {/if}
                <hr class="w3-separator">
    {if allowed('page', SER_CHANGES)}
                <div class="w3-dropdown-hover">
                    <button class="w3-button">Changes</button>
                    <div class="w3-dropdown-content w3-bar-block w3-card-4">
        {if allowed('page', SERC_NOTES)}
                        <a href="{$ROOT}routes/changes/notes.php" class="w3-bar-item w3-button">Notes</a>
        {/if}
        {if allowed('page', SERC_HISTORY)}
                        <a href="{$ROOT}routes/changes/history.php" class="w3-bar-item w3-button">History</a>
        {/if}
        {if allowed('page', SERC_REPORT)}
                        <a href="{$ROOT}routes/changes/report.php" class="w3-bar-item w3-button">Report</a>
        {/if}
                    </div>
                </div>
    {/if}
    {if allowed('page', SE_REPORTS)}
                <div class="w3-dropdown-hover">
                    <button class="w3-button">Reports</button>
                    <div class="w3-dropdown-content w3-bar-block w3-card-4">
        {if allowed('page', SERC_REPORT)}
                        <a href="{$ROOT}routes/changes/report.php" class="w3-bar-item w3-button">Changes</a>
        {/if}
        {if allowed('page', SER_DRAW)}
                        <a href="{$ROOT}routes/reports/draw.php" class="w3-bar-item w3-button">Draw</a>
        {/if}
        {if allowed('page', SER_ROUTE)}
                        <a href="{$ROOT}routes/reports/route.php" class="w3-bar-item w3-button">Route</a>
        {/if}
        {if allowed('page', SER_STATUS)}
                        <a href="{$ROOT}routes/reports/status.php" class="w3-bar-item w3-button">Status</a>
        {/if}
        {if allowed('page', SER_TIPS)}
                        <a href="{$ROOT}routes/reports/tips.php" class="w3-bar-item w3-button">Tips</a>
        {/if}
                    </div>
                </div>
    {/if}
            </div>
        </div>
{/if}
{if allowed('page', S_ADMIN)}
        <div class="w3-dropdown-hover">
            <button class="w3-button">Admin</button>
            <div class="w3-dropdown-content w3-bar-block w3-card-4">
    {if allowed('page', SA_AUDIT)}
                <a href="{$ROOT}admin/auditlog.php" class="w3-bar-item w3-button">Audit Log</a>
    {/if}
    {if allowed('page', SA_BILLING)}
                <a href="{$ROOT}admin/billing.php" class="w3-bar-item w3-button">Billing</a>
    {/if}
    {if allowed('page',  SA_CONFIG)}
                <a href="{$ROOT}admin/config.php" class="w3-bar-item w3-button">Configuration</a>
    {/if}
    {if allowed('page', SA_GROUPS)}
                <a href="{$ROOT}admin/groups.php" class="w3-bar-item w3-button">Groups</a>
    {/if}
    {if allowed('page', SA_PERIODS)}
                <a href="{$ROOT}admin/periods.php" class="w3-bar-item w3-button">Periods</a>
    {/if}
    {if allowed('page', SA_SECURITY)}
                <a href="{$ROOT}admin/security.php" class="w3-bar-item w3-button">Security</a>
    {/if}
    {if allowed('page', SA_ROUTES)}
                <a href="{$ROOT}admin/routes.php" class="w3-bar-item w3-button">Routes</a>
    {/if}
    {if allowed('page', SA_USERS)}
                <a href="{$ROOT}admin/users.php" class="w3-bar-item w3-button">Users</a>
    {/if}
                <hr class="w3-separator">
    {if allowed('page', SA_CUSTOMERS)}
                <div class="w3-dropdown-hover">
                    <button class="w3-button">Customers</button>
                    <div class="w3-dropdown-content w3-bar-block w3-card-4">
        {if allowed('page', SAC_BILLING)}
                        <a href="{$ROOT}admin/customers/billing.php" class="w3-bar-item w3-button">Billing</a>
        {/if}
        {if allowed('page', SAC_RATES)}
                        <a href="{$ROOT}admin/customers/rates.php" class="w3-bar-item w3-button">Rates</a>
        {/if}
        {if allowed('page', SAC_TYPES)}
                        <a href="{$ROOT}admin/customers/types.php" class="w3-bar-item w3-button">Types</a>
        {/if}
                    </div>
                </div>
    {/if}
            </div>
        </div>
{/if}
{if allowed('page', S_PROFILE)}
        <a href="{$ROOT}profile.php" class="w3-bar-item w3-button">Profile</a>
{/if}
        <a href="{$ROOT}logout.php" class="w3-bar-item w3-button">Logout</a>
    </div>
</div>
