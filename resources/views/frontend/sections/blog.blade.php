@extends('layouts.layout')
@section('styles')
<link rel="stylesheet" href="{{ asset('frontend/assets/css/blog.css') }}">
<style>
    .pagination_fg {
        display: flex !important;
        justify-content: center;
        align-items: center;
        margin-top: 40px;
        margin-bottom: 40px;
        padding: 15px 0;
        width: 100%;
        overflow: hidden;
    }
    
    /* Hide pagination info text but keep pagination list */
    .pagination_fg > p,
    .pagination_fg > div:not(ul):not(.pagination):not(nav),
    .pagination_fg > span:not(.pagination) {
        display: none !important;
    }
    
    /* Ensure pagination is visible and in one row */
    .pagination_fg ul,
    .pagination_fg .pagination,
    .pagination_fg nav,
    .pagination_fg nav ul,
    .pagination_fg > nav {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        justify-content: center;
        align-items: center;
        list-style: none !important;
        padding: 0 !important;
        margin: 0 auto !important;
        gap: 12px !important;
        flex-wrap: nowrap !important;
        white-space: nowrap;
    }
    
    .pagination_fg .pagination,
    .pagination_fg ul.pagination {
        display: flex !important;
        list-style: none !important;
        padding: 0 !important;
        margin: 0 auto !important;
        gap: 12px !important;
        flex-wrap: nowrap !important;
        justify-content: center;
        align-items: center;
        white-space: nowrap;
    }
    
    .pagination_fg .pagination li,
    .pagination_fg ul.pagination li,
    .pagination_fg ul li,
    .pagination_fg nav ul li {
        margin: 0 !important;
        display: inline-block !important;
        list-style: none !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    /* Hide all page numbers - only show Previous/Next */
    .pagination_fg .pagination li:not(:first-child):not(:last-child),
    .pagination_fg ul.pagination li:not(:first-child):not(:last-child),
    .pagination_fg ul li:not(:first-child):not(:last-child),
    .pagination_fg nav ul li:not(:first-child):not(:last-child) {
        display: none !important;
    }
    
    /* Also hide if it doesn't have prev/next rel attribute and is not first/last */
    .pagination_fg .pagination li:not([rel="prev"]):not([rel="next"]):not(:first-child):not(:last-child),
    .pagination_fg ul.pagination li:not([rel="prev"]):not([rel="next"]):not(:first-child):not(:last-child) {
        display: none !important;
    }
    
    .pagination_fg .pagination li a,
    .pagination_fg .pagination li span,
    .pagination_fg ul.pagination li a,
    .pagination_fg ul.pagination li span,
    .pagination_fg ul li a,
    .pagination_fg ul li span,
    .pagination_fg nav ul li a,
    .pagination_fg nav ul li span {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        min-width: 30px;
        height: 30px;
        padding: 4px 8px;
        text-decoration: none !important;
        border: 1px solid #e9ecef !important;
        border-radius: 4px;
        color: #495057 !important;
        font-weight: 600;
        font-size: 12px;
        transition: all 0.2s ease;
        background-color: #fff !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.06);
        cursor: pointer;
        visibility: visible !important;
        opacity: 1 !important;
        white-space: nowrap;
    }
    
    .pagination_fg .pagination li a:hover {
        background: linear-gradient(135deg, #152a70 0%, #1e3a8a 100%) !important;
        color: #fff !important;
        border-color: #152a70 !important;
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(21, 42, 112, 0.25);
    }
    
    /* Mobile touch-friendly styles */
    @media (max-width: 768px) {
        .pagination_fg .pagination li a,
        .pagination_fg .pagination li span,
        .pagination_fg ul.pagination li a,
        .pagination_fg ul.pagination li span {
            -webkit-tap-highlight-color: rgba(21, 42, 112, 0.2);
            touch-action: manipulation;
        }
        
        .pagination_fg .pagination li a:active {
            transform: scale(0.98);
            box-shadow: 0 1px 3px rgba(21, 42, 112, 0.2);
        }
    }
    
    .pagination_fg .pagination li.active span,
    .pagination_fg ul.pagination li.active span,
    .pagination_fg nav ul li.active span {
        background: linear-gradient(135deg, #152a70 0%, #1e3a8a 100%) !important;
        color: #fff !important;
        border-color: #152a70 !important;
        box-shadow: 0 3px 8px rgba(21, 42, 112, 0.25);
        font-weight: 700;
    }
    
    .pagination_fg .pagination li.disabled span,
    .pagination_fg .pagination li.disabled a,
    .pagination_fg ul.pagination li.disabled span,
    .pagination_fg ul.pagination li.disabled a {
        color: #adb5bd !important;
        cursor: not-allowed !important;
        opacity: 0.5;
        background-color: #f8f9fa !important;
        border-color: #e9ecef !important;
        box-shadow: none;
        pointer-events: none;
    }
    
    .pagination_fg .pagination li.disabled span:hover,
    .pagination_fg .pagination li.disabled a:hover {
        transform: none;
        box-shadow: none;
        background-color: #f8f9fa !important;
    }
    
    /* Style Previous/Next buttons - make them prominent and beautiful */
    .pagination_fg .pagination li a[rel="prev"],
    .pagination_fg .pagination li a[rel="next"],
    .pagination_fg ul.pagination li a[rel="prev"],
    .pagination_fg ul.pagination li a[rel="next"],
    .pagination_fg .pagination li:first-child a,
    .pagination_fg .pagination li:first-child span,
    .pagination_fg .pagination li:last-child a,
    .pagination_fg .pagination li:last-child span {
        min-width: 90px !important;
        width: auto !important;
        height: 38px !important;
        padding: 8px 20px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        background: linear-gradient(135deg, #152a70 0%, #1e3a8a 100%) !important;
        color: #fff !important;
        border: 2px solid #152a70 !important;
        border-radius: 25px !important;
        box-shadow: 0 4px 12px rgba(21, 42, 112, 0.3) !important;
        transition: all 0.3s ease !important;
    }
    
    .pagination_fg .pagination li a[rel="prev"]:hover,
    .pagination_fg .pagination li a[rel="next"]:hover,
    .pagination_fg ul.pagination li a[rel="prev"]:hover,
    .pagination_fg ul.pagination li a[rel="next"]:hover,
    .pagination_fg .pagination li:first-child a:hover,
    .pagination_fg .pagination li:last-child a:hover {
        background: linear-gradient(135deg, #1e3a8a 0%, #152a70 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(21, 42, 112, 0.4) !important;
        border-color: #1e3a8a !important;
    }
    
    .pagination_fg .pagination li.disabled a[rel="prev"],
    .pagination_fg .pagination li.disabled a[rel="next"],
    .pagination_fg .pagination li.disabled:first-child a,
    .pagination_fg .pagination li.disabled:last-child a,
    .pagination_fg .pagination li.disabled:first-child span,
    .pagination_fg .pagination li.disabled:last-child span {
        background: #e9ecef !important;
        color: #adb5bd !important;
        border-color: #dee2e6 !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
        opacity: 0.6;
    }
    
    .pagination_fg .pagination li.disabled a[rel="prev"]:hover,
    .pagination_fg .pagination li.disabled a[rel="next"]:hover {
        transform: none !important;
        background: #e9ecef !important;
        box-shadow: none !important;
    }
    
    /* Responsive pagination - maintain style on mobile */
    @media (max-width: 768px) {
        .pagination_fg {
            margin-top: 30px;
            margin-bottom: 30px;
            padding: 15px 10px;
        }
        
        .pagination_fg .pagination,
        .pagination_fg ul.pagination,
        .pagination_fg nav ul {
            gap: 12px !important;
            flex-wrap: nowrap !important;
        }
        
        .pagination_fg .pagination li a,
        .pagination_fg .pagination li span,
        .pagination_fg ul.pagination li a,
        .pagination_fg ul.pagination li span,
        .pagination_fg ul li a,
        .pagination_fg ul li span,
        .pagination_fg nav ul li a,
        .pagination_fg nav ul li span {
            min-width: 30px;
            height: 30px;
            padding: 4px 8px;
            font-size: 12px;
        }
        
        .pagination_fg .pagination li a[rel="prev"],
        .pagination_fg .pagination li a[rel="next"],
        .pagination_fg ul.pagination li a[rel="prev"],
        .pagination_fg ul.pagination li a[rel="next"],
        .pagination_fg .pagination li:first-child a,
        .pagination_fg .pagination li:first-child span,
        .pagination_fg .pagination li:last-child a,
        .pagination_fg .pagination li:last-child span {
            min-width: 80px !important;
            width: auto !important;
            height: 36px !important;
            padding: 7px 16px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            background: linear-gradient(135deg, #152a70 0%, #1e3a8a 100%) !important;
            color: #fff !important;
            border: 2px solid #152a70 !important;
            border-radius: 20px !important;
            box-shadow: 0 4px 12px rgba(21, 42, 112, 0.3) !important;
        }
    }
    
    @media (max-width: 576px) {
        .pagination_fg {
            margin-top: 25px;
            margin-bottom: 25px;
            padding: 12px 8px;
        }
        
        .pagination_fg .pagination,
        .pagination_fg ul.pagination,
        .pagination_fg nav ul {
            gap: 10px !important;
            flex-wrap: nowrap !important;
        }
        
        .pagination_fg .pagination li a,
        .pagination_fg .pagination li span,
        .pagination_fg ul.pagination li a,
        .pagination_fg ul.pagination li span,
        .pagination_fg ul li a,
        .pagination_fg ul li span,
        .pagination_fg nav ul li a,
        .pagination_fg nav ul li span {
            min-width: 28px;
            height: 28px;
            padding: 3px 6px;
            font-size: 11px;
        }
        
        .pagination_fg .pagination li a[rel="prev"],
        .pagination_fg .pagination li a[rel="next"],
        .pagination_fg ul.pagination li a[rel="prev"],
        .pagination_fg ul.pagination li a[rel="next"],
        .pagination_fg .pagination li:first-child a,
        .pagination_fg .pagination li:first-child span,
        .pagination_fg .pagination li:last-child a,
        .pagination_fg .pagination li:last-child span {
            min-width: 75px !important;
            width: auto !important;
            height: 34px !important;
            padding: 6px 14px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            background: linear-gradient(135deg, #152a70 0%, #1e3a8a 100%) !important;
            color: #fff !important;
            border: 2px solid #152a70 !important;
            border-radius: 18px !important;
            box-shadow: 0 3px 10px rgba(21, 42, 112, 0.3) !important;
        }
    }
    
    @media (max-width: 400px) {
        .pagination_fg {
            padding: 10px 5px;
        }
        
        .pagination_fg .pagination,
        .pagination_fg ul.pagination,
        .pagination_fg nav ul {
            gap: 8px !important;
        }
        
        .pagination_fg .pagination li a[rel="prev"],
        .pagination_fg .pagination li a[rel="next"],
        .pagination_fg ul.pagination li a[rel="prev"],
        .pagination_fg ul.pagination li a[rel="next"],
        .pagination_fg .pagination li:first-child a,
        .pagination_fg .pagination li:first-child span,
        .pagination_fg .pagination li:last-child a,
        .pagination_fg .pagination li:last-child span {
            min-width: 70px !important;
            height: 32px !important;
            padding: 5px 12px !important;
            font-size: 11px !important;
            background: linear-gradient(135deg, #152a70 0%, #1e3a8a 100%) !important;
            color: #fff !important;
            border: 2px solid #152a70 !important;
            border-radius: 16px !important;
            box-shadow: 0 3px 8px rgba(21, 42, 112, 0.3) !important;
        }
    }
</style>
@endsection
@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Blog",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "https://tazen.in/blog"
  },
  "name": "Tazen Blog",
  "description": "Explore expert articles on career, health, finance, and lifestyle from verified professionals at Tazen.in.",
  "url": "https://tazen.in/blog",
  "publisher": {
    "@type": "Organization",
    "name": "Tazen",
    "logo": {
      "@type": "ImageObject",
      "url": "https://tazen.in/frontend/assets/img/tazen-logo.png"
    }
  }
}
</script>
@endsection

@section('content')
<main class="bg_color">
    @foreach($blogbanners as $banner)
    <!-- blog header  -->
    <div class="hero_single blog-head" style="background-image: url('{{ asset('storage/' . $banner->banner_image) }}');">
        <!-- Content inside the div, if needed -->    
     <div class="opacity-mask" data-opacity-mask="rgba(0, 0, 0, 0.4)">
         <div class="container">
             <div class="row">
                 <div class="col-xl-12 col-lg-12">
                     <h1>{{ $banner->heading }}</h1>
                     <h2>{{ $banner->subheading }}</h2>
                     
                 </div>
             </div>
             <!-- /row -->
         </div>
     </div>
 </div>
 @endforeach
     <!-- blog header section end  -->
     <div class="container margin_45_40">			
         <div class="row">
             <div class="col-lg-12 pb-5">
                 <div class="widget search_blog">
                    <form action="{{ route('blog.index') }}" method="GET">
                        <div class="form-group">
                            <input type="text" name="search" id="search" class="form-control" placeholder="Search Blogs or Categories..." value="{{ request('search') }}">
                            <span><input type="submit" value="Search"></span>
                        </div>
                    </form>
                 </div>
             </div>
             <aside class="col-lg-3">
                 
                 <!-- /widget -->
                 <div class="widget">
                    <div class="widget-title first">
                        <h4>Latest Post</h4>
                    </div>
                    <ul class="comments-list">
                        @foreach($latestBlogs as $latestBlog)
                        <li>
                            <div class="alignleft">
                                <a href="{{ route('blog.show', \Illuminate\Support\Str::slug($latestBlog->blog->title)) }}">
                                    <img src="{{ asset('storage/' . $latestBlog->image) }}" alt="{{ $latestBlog->blog_id }}">
                                </a>
                            </div>
                            <small>Category - {{ $latestBlog->category }} - {{ $latestBlog->created_at->format('d M Y') }}</small>
                            <p><b>{{ $latestBlog->blog->title }}</b></p>
                            <h3><a href="{{ route('blog.show', \Illuminate\Support\Str::slug($latestBlog->blog->title)) }}" title="{{ $latestBlog->blog->title }}">{{ \Illuminate\Support\Str::limit($latestBlog->blog->title, 50) }}</a></h3>
                        </li>
                        @endforeach
                    </ul>
                </div>
                
                 <!-- /widget -->
                 <div class="widget">
                    <div class="widget-title">
                        <h4>Categories</h4>
                    </div>
                    <ul class="cats">
                        @foreach($categoryCounts as $category)
                            <li>
                                <a href="{{ route('blog.index', ['category' => $category->category]) }}">{{ $category->category }} <span>({{ $category->post_count }})</span></a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                
                 <!-- /widget -->
                 <div class="widget">
                    <div class="widget-title">
                        <h4>Popular Tags</h4>
                    </div>
                    <div class="tags">
                        @foreach($categoryCounts as $category)
                            <a href="{{ route('blog.index', ['category' => $category->category]) }}">{{ $category->category }}</a>
                        @endforeach
                    </div>
                </div>
                
                 <!-- /widget -->
             </aside>
             <!-- /aside -->


             <div class="col-lg-9">
                <div class="row">
                    @foreach($blogPosts as $blogPost)
                        <div class="col-md-4">
                            <article class="blog" style="max-width: 100%; margin-bottom: 20px;">
                                <figure style="height: 200px; overflow: hidden;">
                                    <a href="{{ route('blog.show', \Illuminate\Support\Str::slug($blogPost->blog->title)) }}">
                                        <img src="{{ asset('storage/' . $blogPost->image) }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                        <div class="preview"><span>Read more</span></div>
                                    </a>
                                </figure>
                                <div class="post_info" style="padding: 15px;">
                                    <small>{{ $blogPost->category }} - {{ $blogPost->created_at->format('d M Y') }}</small>
                                    <h3 style="font-size: 1.2rem; margin: 10px 0;"><a href="{{ route('blog.show', \Illuminate\Support\Str::slug($blogPost->blog->title)) }}">{{ $blogPost->blog->title }}</a></h3>
                                    <p style="font-size: 0.9rem;">{{ \Illuminate\Support\Str::words(strip_tags($blogPost->content ?? ''), 30, '...') }}</p>
                                    <ul style="margin-top: 10px;">
                                        <li>
                                            <div class="thumb" style="width: 30px; height: 30px;">
                                                <img src="{{ asset('storage/' . $blogPost->author_avatar) }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <a href="#" style="font-size: 0.9rem;">{{ $blogPost->author_name }}</a> 
                                        </li>
                                        <li>
                                            <a href="#" style="font-size: 0.9rem;"><i class="icon_comment_alt"></i> {{ $blogPost->comment_count}}</a>
                                        </li>
                                    </ul>
                                </div>
                            </article>
                            <!-- /article -->
                        </div>
                    @endforeach
                </div>
            
                <!-- Pagination -->
                <div class="pagination_fg mt-4">
                    {!! $blogPosts->appends(request()->query())->links() !!}
                </div>
            </div>
            
         <!-- /row -->	
     </div>
     <!-- /container -->
     
 </main>

@endsection