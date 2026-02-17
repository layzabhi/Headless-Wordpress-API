'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import ProjectCard from '../components/ProjectCard';
import type { Project } from '../lib/types';

export default function HomePage() {
  const [projects, setProjects] = useState<Project[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch('https://localhost/projects/wp-json/wp/v2/projects')
      .then(res => res.json())
      .then(data => {
        setProjects(data);
        setLoading(false);
      })
      .catch(err => {
        console.error(err);
        setLoading(false);
      });
  }, []);

  return (
    <main>
      {/* Hero Section */}
      <section className="relative bg-gradient-to-r from-blue-600 to-blue-800 text-white">
        <div className="absolute inset-0 opacity-10">
          <div className="absolute inset-0" style={{
            backgroundImage: 'radial-gradient(circle at 2px 2px, white 1px, transparent 0)',
            backgroundSize: '40px 40px'
          }} />
        </div>

        <div className="relative max-w-7xl mx-auto px-4 py-24 sm:py-32">
          <div className="text-center">
            <h1 className="text-4xl sm:text-6xl font-bold mb-6">
              Welcome to My Portfolio
            </h1>
            <p className="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
              Building modern web experiences with WordPress and React.
            </p>
            <div className="flex gap-4 justify-center">
              <Link href="/projects" 
                className="bg-white text-blue-600 px-6 py-3 rounded-md font-semibold hover:bg-blue-50 transition transform hover:scale-105">
                View My Work
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* Projects Section */}
      <section className="py-16 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold text-gray-900 mb-4">Recent Projects</h2>
            <p className="text-lg text-gray-600">Check out my latest work</p>
          </div>

          {loading ? (
            <div className="flex justify-center py-12">
              <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600" />
            </div>
          ) : projects.length === 0 ? (
            <div className="text-center py-12">
              <p className="text-gray-600">No projects yet. Check back soon!</p>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              {projects.map(project => (
                <ProjectCard key={project.id} project={project} />
              ))}
            </div>
          )}
        </div>
      </section>

      {/* Tech Stack */}
      <section className="py-16">
        <div className="max-w-7xl mx-auto px-4">
          <h2 className="text-3xl font-bold text-center mb-12">Technologies</h2>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
            {['WordPress', 'React', 'Next.js', 'TypeScript', 'PHP', 'Tailwind', 'Node.js', 'REST API'].map(tech => (
              <div key={tech} 
                className="p-6 bg-gray-50 rounded-lg text-center hover:bg-blue-50 transition">
                <span className="font-semibold">{tech}</span>
              </div>
            ))}
          </div>
        </div>
      </section>
    </main>
  );
}