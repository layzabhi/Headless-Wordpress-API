'use client';

import { useEffect, useState } from 'react';
import ProjectCard from '../../components/ProjectCard';
import type { Project } from '../../lib/types';

export default function ProjectsPage() {
  const [projects, setProjects] = useState<Project[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch('https://localhost/projects/wp-json/wp/v2/projects')
      .then(res => res.json())
      .then(data => {
        setProjects(data);
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }, []);

  return (
    <div className="bg-white min-h-screen">
      <div className="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
        <div className="max-w-7xl mx-auto px-4">
          <h1 className="text-4xl font-bold mb-4">My Projects</h1>
          <p className="text-xl text-blue-100">
            A showcase of my work and creative solutions
          </p>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 py-16">
        {loading ? (
          <div className="flex justify-center py-12">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600" />
          </div>
        ) : (
          <>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              {projects.map(project => (
                <ProjectCard key={project.id} project={project} />
              ))}
            </div>
            <div className="mt-12 text-center text-gray-600">
              Showing {projects.length} {projects.length === 1 ? 'project' : 'projects'}
            </div>
          </>
        )}
      </div>
    </div>
  );
}