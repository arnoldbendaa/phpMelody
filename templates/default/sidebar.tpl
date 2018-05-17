<div class="sidebar">




    <div class="sidebar-block">
        <div class="sidebar-title"><span class="highlight">A-Z </span>{pageName}</div>
        <div class="menu-letters">
            <div class="alphabet">
                <ul>
                    <li><a href="#" class="alphabet-link">a</a>
                    </li>
                    <li><a href="#" class="alphabet-link">b</a>
                    </li>
                    <li class="active"><a href="#" class="alphabet-link">c</a>
                    </li>
                    <li><a href="#" class="alphabet-link">d</a>
                    </li>
                    <li><a href="#" class="alphabet-link">e</a>
                    </li>
                    <li><a href="#" class="alphabet-link">f</a>
                    </li>
                    <li><a href="#" class="alphabet-link">g</a>
                    </li>
                    <li><a href="#" class="alphabet-link">h</a>
                    </li>
                    <li><a href="#" class="alphabet-link">i</a>
                    </li>
                    <li><a href="#" class="alphabet-link">j</a>
                    </li>
                    <li><a href="#" class="alphabet-link">k</a>
                    </li>
                    <li><a href="#" class="alphabet-link">l</a>
                    </li>
                    <li><a href="#" class="alphabet-link">m</a>
                    </li>
                    <li><a href="#" class="alphabet-link">n</a>
                    </li>
                    <li><a href="#" class="alphabet-link">o</a>
                    </li>
                    <li><a href="#" class="alphabet-link">p</a>
                    </li>
                    <li><a href="#" class="alphabet-link">q</a>
                    </li>
                    <li><a href="#" class="alphabet-link">r</a>
                    </li>
                    <li><a href="#" class="alphabet-link">s</a>
                    </li>
                    <li><a href="#" class="alphabet-link">t</a>
                    </li>
                    <li><a href="#" class="alphabet-link">u</a>
                    </li>
                    <li><a href="#" class="alphabet-link">v</a>
                    </li>
                    <li><a href="#" class="alphabet-link">w</a>
                    </li>
                    <li><a href="#" class="alphabet-link">x</a>
                    </li>
                    <li><a href="#" class="alphabet-link">y</a>
                    </li>
                    <li><a href="#" class="alphabet-link">z</a>
                    </li>
                    <li><a href="#" class="alphabet-link">all</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>






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
                    <span class="text-truncate">{smarty_fewchars s=$casinos_data[1].name length=32}</span></span>
                </a>
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
