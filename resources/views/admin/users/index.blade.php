@extends('layouts.admin')
@section('title','Users')
@section('page_title','User Management')
@section('page_subtitle','Manage admin accounts and permissions')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
  @foreach(['total'=>['Total','#00B4D8'],'pending'=>['Pending','#FFB703'],'active'=>['Active','#10B981'],'blocked'=>['Blocked','#EF4444']] as $k=>[$l,$c])
    <a href="{{ route('admin.users', ['status'=>$k==='total'?'all':$k]) }}"
       class="bg-[#1a2540] border border-white/5 rounded-xl p-4 text-center hover:border-white/10 transition-colors">
      <p class="text-2xl font-extrabold font-heading" style="color:{{ $c }}">{{ $counts[$k] }}</p>
      <p class="text-gray-400 text-xs mt-1">{{ $l }}</p>
    </a>
  @endforeach
</div>

{{-- Filters --}}
<div class="flex flex-wrap gap-2 mb-6">
  @foreach(['all'=>'All Roles','super_admin'=>'Super Admin','admin'=>'Admin','article_writer'=>'Article Writers'] as $r=>$l)
    <a href="{{ route('admin.users', array_merge(request()->query(), ['role'=>$r])) }}"
       class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all
              {{ $role === $r ? 'bg-pm-cyan text-white border-pm-cyan' : 'bg-white/5 text-gray-400 border-white/10 hover:border-white/20' }}">
      {{ $l }}
    </a>
  @endforeach
</div>

<div class="bg-[#1a2540] border border-white/5 rounded-2xl overflow-hidden">
  @if($users->count() > 0)
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-white/5">
          <tr>
            <th class="text-left px-5 py-3.5 text-gray-500 text-xs uppercase tracking-wide font-semibold">User</th>
            <th class="text-left px-5 py-3.5 text-gray-500 text-xs uppercase tracking-wide font-semibold hidden md:table-cell">Role</th>
            <th class="text-left px-5 py-3.5 text-gray-500 text-xs uppercase tracking-wide font-semibold">Status</th>
            <th class="text-left px-5 py-3.5 text-gray-500 text-xs uppercase tracking-wide font-semibold hidden lg:table-cell">Joined</th>
            <th class="text-right px-5 py-3.5 text-gray-500 text-xs uppercase tracking-wide font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
          @foreach($users as $u)
            <tr class="hover:bg-white/3 transition-colors">
              <td class="px-5 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-pm-cyan/10 flex items-center justify-center flex-shrink-0">
                    <span class="text-pm-cyan font-bold text-sm">{{ strtoupper(substr($u->full_name,0,1)) }}</span>
                  </div>
                  <div>
                    <p class="text-white font-medium text-sm">{{ $u->full_name }}</p>
                    <p class="text-gray-500 text-xs">{{ $u->email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-5 py-4 hidden md:table-cell">
                <form action="{{ route('admin.users.role', $u->id) }}" method="POST" class="flex items-center gap-2">
                  @csrf @method('PATCH')
                  <select name="role" onchange="this.form.submit()"
                          {{ $u->role === 'super_admin' || $u->id === auth('admin')->id() ? 'disabled' : '' }}
                          class="bg-white/5 border border-white/10 rounded-lg px-2 py-1.5 text-xs text-white focus:outline-none focus:border-pm-cyan disabled:opacity-50">
                    <option value="super_admin"    {{ $u->role === 'super_admin'    ? 'selected' : '' }}>Super Admin</option>
                    <option value="admin"          {{ $u->role === 'admin'          ? 'selected' : '' }}>Admin</option>
                    <option value="article_writer" {{ $u->role === 'article_writer' ? 'selected' : '' }}>Article Writer</option>
                  </select>
                </form>
              </td>
              <td class="px-5 py-4">
                <form action="{{ route('admin.users.status', $u->id) }}" method="POST" class="flex items-center gap-2">
                  @csrf @method('PATCH')
                  <select name="status" onchange="this.form.submit()"
                          {{ $u->role === 'super_admin' ? 'disabled' : '' }}
                          class="bg-white/5 border border-white/10 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:border-pm-cyan disabled:opacity-50
                            {{ $u->status === 'active' ? 'text-green-400' : ($u->status === 'pending' ? 'text-yellow-400' : 'text-red-400') }}">
                    <option value="active"  {{ $u->status === 'active'  ? 'selected' : '' }}>Active</option>
                    <option value="pending" {{ $u->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="blocked" {{ $u->status === 'blocked' ? 'selected' : '' }}>Blocked</option>
                  </select>
                </form>
              </td>
              <td class="px-5 py-4 hidden lg:table-cell">
                <p class="text-gray-500 text-xs">{{ $u->created_at->format('M j, Y') }}</p>
                @if($u->last_login_at)
                  <p class="text-gray-600 text-[10px]">Login: {{ $u->last_login_at->diffForHumans() }}</p>
                @endif
              </td>
              <td class="px-5 py-4 text-right">
                @if($u->id !== auth('admin')->id() && $u->role !== 'super_admin')
                  <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST"
                        onsubmit="return confirm('Delete user {{ addslashes($u->full_name) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1.5 text-gray-500 hover:text-red-400 rounded-lg hover:bg-red-500/10 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                  </form>
                @else
                  <span class="text-gray-600 text-xs">Protected</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @if($users->hasPages())
      <div class="px-5 py-4 border-t border-white/5">{{ $users->links() }}</div>
    @endif
  @else
    <div class="py-16 text-center text-gray-500">No users found.</div>
  @endif
</div>

@endsection