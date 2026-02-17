
'use client';

import { useEffect, useState } from 'react';
import { useParams } from 'next/navigation';
import Link from 'next/link';
import Image from 'next/image';
import type { Project } from '../../../lib/types';

export default function ProjectPage() {
  const params = useParams();
  const [project, setProject] = useState<Project | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (params.slug) {
      fetch(`https://localhost/projects/wp-json/wp/v2/projects?slug=${params.slug}`)
        .then(res => res.json())
        .then(data => {
          setProject(data[0] || null);
          setLoading(false);
        })
        .catch(() => setLoading(false));
    }
  }, [params.slug]);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600" />
      </div>
    );
  }

  if (!project) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <h1 className="text-2xl font-bold mb-4">Project Not Found</h1>
          <Link href="/projects" className="text-blue-600 hover:text-blue-800">
            Back to Projects
          </Link>
        </div>
      </div>
    );
  }

  const techs = project.acf?.technologies || [];
  const results = project.acf?.results || [];

  return (
    <article className="bg-white">
      <div className="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
        <div className="max-w-7xl mx-auto px-4">
          <Link href="/projects" 
            className="inline-flex items-center text-blue-100 hover:text-white mb-6 transition">
            <svg className="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
            </svg>
            Back
          </Link>

          <h1 className="text-4xl font-bold mb-4">{project.title.rendered}</h1>
          
          <div className="flex flex-wrap gap-4 text-blue-100">
            {project.acf?.client && <div>Client: {project.acf.client}</div>}
            {project.acf?.year && <div>Year: {project.acf.year}</div>}
            {project.acf?.project_url && (
              <a href={project.acf.project_url} target="_blank" rel="noopener noreferrer"
                className="hover:text-white transition">
                Visit Site →
              </a>
            )}
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 py-16">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-12">
          <div className="lg:col-span-2">
            <div className="prose prose-lg max-w-none"
              dangerouslySetInnerHTML={{ __html: project.content.rendered }} />
          </div>

          <div className="lg:col-span-1">
            {techs.length > 0 && (
              <div className="bg-gray-50 rounded-lg p-6 mb-8 sticky top-24">
                <h3 className="font-bold mb-4">Technologies</h3>
                <div className="flex flex-wrap gap-2">
                  {techs.map((tech: string) => (
                    <span key={tech} 
                      className="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                      {tech}
                    </span>
                  ))}
                </div>
              </div>
            )}

            {results.length > 0 && (
              <div className="bg-gray-50 rounded-lg p-6">
                <h3 className="font-bold mb-4">Results</h3>
                <div className="space-y-4">
                  {results.map((result, i) => (
                    <div key={i} className="border-l-4 border-blue-600 pl-4">
                      <div className="text-2xl font-bold text-blue-600">{result.value}</div>
                      <div className="text-sm font-medium">{result.metric}</div>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </article>
  );
}