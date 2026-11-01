<div class="collapse show" id="collapseFilter">
    <div class="block block-rounded h-100 mb-3">
        <div class="block-header block-header-default">
            <h3 class="block-title">Filter</h3>
            <div class="block-options">
                <a class="text-dark" data-bs-toggle="collapse" href="#collapseFilter" role="button" aria-controls="collapseFilter">
                    <i class="si si-close"></i>
                </a>
            </div>
        </div>
        <div class="block-content">
            <form class="filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="search">Search</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Title or body">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select js-select2" id="status" name="status">
                                <option value="">All</option>
                                <option value="unread">Unread</option>
                                <option value="read">Read</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="date_from">Date From</label>
                            <input type="text" class="form-control js-flatpickr-date" id="date_from" name="date_from" placeholder="YYYY-MM-DD" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="date_to">Date To</label>
                            <input type="text" class="form-control js-flatpickr-date" id="date_to" name="date_to" placeholder="YYYY-MM-DD" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="text-end mb-3">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-fw fa-search"></i> Search
                            </button>
                            <button type="button" class="ms-2 btn btn-sm btn-outline-danger reset-data">
                                <i class="fa fa-fw fa-sync"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
