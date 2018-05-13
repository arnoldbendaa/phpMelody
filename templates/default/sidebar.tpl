<div class="sidebar">
    <div class="sidebar-block">
        <div class="sidebar-title"><span class="highlight">Top </span>Categories</div>
        <ul class="sidebar-lists">
            <li class="sidebar-lists-item">
                <a  href="{$categories_data[1].url}" class="sidebar-lists-link clearfix"><span class="name wrap-icon">
							<svg class="svg-icon" width="40px" height="37px">
								<use xlink:href="#list"></use>
							</svg>
							<span class="text-truncate">{smarty_fewchars s=$categories_data[1].name length=32}</span></span>
                </a>
            </li>

        </ul>
    </div>
    <div class="sidebar-block">
        <div class="sidebar-title"><span class="highlight">Top </span>Casinos</div>
        <ul class="sidebar-lists">
            <li class="sidebar-lists-item">
                <a href="{$casinos_data[1].url}" class="sidebar-lists-link clearfix">
							<span class="name wrap-icon">
								<svg class="svg-icon" width="11px" height="10.969px">
									<use xlink:href="#model"></use>
								</svg>
							<span class="text-truncate">{smarty_fewchars s=$casinos_data[1].name length=32}</span></span></a>
            </li>
        </ul>
    </div>
    <div class="sidebar-block">
        <div class="sidebar-title"><span class="highlight">Top </span>Providers</div>
        <ul class="sidebar-lists">
            <li class="sidebar-lists-item">
                <a  href="{$providers_data[1].url}" class="sidebar-lists-link clearfix">
							<span class="name wrap-icon">
								<svg class="svg-icon" width="11px" height="10.969px">
									<use xlink:href="#link"></use>
								</svg>
							<span class="text-truncate">{smarty_fewchars s=$providers_data[1].name length=32}</span>
							</span>
                </a>
            </li>
        </ul>
    </div>
</div>
