<x-profile :sharedData="$sharedData">
  @section('title')
      Post
  @endsection
  @include('profile-posts-only')
</x-profile>