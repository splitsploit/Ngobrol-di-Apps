<x-profile :sharedData="$sharedData">
    @section('title')
      Followers
    @endsection
    @include('profile-followers-only')
</x-profile>