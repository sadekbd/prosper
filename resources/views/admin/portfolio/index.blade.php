@extends('layouts.admin')
@section('title','Portfolio')
@section('page_title','Portfolio Projects')
@section('page_subtitle','Manage case studies and projects')

@section('content')

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
  <div class="flex flex-wrap gap-2">
    @foreach(['all'=>'All','published'=>'Published','draft'=>'Draft'] as $s=>$l)
      <a href="{{ route('admin.portfolio', array_merge(request()->query(), ['status'=>$s])) }}"
         class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all
                {{ $status === $s ? 'bg-pm-cyan text-white border-pm-cyan' : 'bg-white/5 text-gray-400 border-white/10 hover:border-white/20' }}">
        {{ $l }}
      </a>
    @endforeach
  </div>
  <a href="{{ route('admin.portfolio.create') }}" class="btn-primary text-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    New Project
  </a>
</div>

<div class="bg-[#1a2540] border border-white/5 rounded-2xl overflow-hidden">
  @if($projects->count() > 0)
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="border-b border-white/5">
          <tr>
            <th class="text-left px-5 py-3.5 text-gray-500 text-xs uppercase tracking-wide font-semibold">Project</th>
            <th class="text-left px-5 py-3.5 text-gray-500 text-xs uppercase tracking-wide font-semibold hidden md:table-cell">Category</th>
            <th class="text-left px-5 py-3.5 text-gray-500 text-xs uppercase tracking-wide font-semibold">Status</th>
            <th class="text-right px-5 py-3.5 text-gray-500 text-xs uppercase tracking-wide font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-white/5">
          @foreach($projects as $project)
            <tr class="hover:bg-white/3 transition-colors">
              <td class="px-5 py-4">
                <div class="flex items-center gap-3">
                  @if($project->is_featured)
                    <span class="text-pm-gold text-xs" title="Featured">⭐</span>
                  @endif
                  <div>
                    <p class="text-white font-medium text-sm">{{ $project->title }}</p>
                    @if($project->result_summary)
                      <p class="text-green-400 text-xs mt-0.5">📈 {{ $project->result_summary }}</p>
                    @endif
                  </div>
                </div>
              </td>
              <td class="px-5 py-4 hidden md:table-cell">
                <span class="px-2 py-1 bg-pm-cyan/10 text-pm-cyan text-[10px] font-semibold rounded-lg">
                  {{ $project->category_label }}
                </span>
              </td>
              <td class="px-5 py-4">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold
                  {{ $project->status === 'published' ? 'bg-green-500/10 text-green-400' : 'bg-gray-500/10 text-gray-400' }}">
                  {{ ucfirst($project->status) }}
                </span>
              </td>
              <td class="px-5 py-4">
                <div class="flex items-center justify-end gap-2">
                  @if($project->status === 'published')
                    <a href="{{ route('portfolio.show', $project->slug) }}" target="_blank"
                       class="p-1.5 text-gray-400 hover:text-pm-cyan rounded-lg hover:bg-white/10 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                  @endif
                  <a href="{{ route('admin.portfolio.edit', $project->id) }}"
                     class="p-1.5 text-gray-400 hover:text-white rounded-lg hover:bg-white/10 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </a>
                  <form action="{{ route('admin.portfolio.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Delete this project?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1.5 text-gray-500 hover:text-red-400 rounded-lg hover:bg-red-500/10 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @if($projects->hasPages())
      <div class="px-5 py-4 border-t border-white/5">{{ $projects->links() }}</div>
    @endif
  @else
    <div class="py-16 text-center">
      <p class="text-gray-400">No projects yet.</p>
      <a href="{{ route('admin.portfolio.create') }}" class="text-pm-cyan text-sm hover:underline mt-2 block">Add your first project →</a>
    </div>
  @endif
</div>

@endsection