import Link from 'next/link';
import Image from 'next/image';
import type { Project } from '../lib/types';

interface Props {
  project: Project;
}

export default function ProjectCard({ project }: Props) {
  const title = project.title.rendered;
  const excerpt = project.excerpt.rendered.replace(/<[^>]*>/g, '').substring(0, 150);
  const image = project.featured_image?.medium?.url || '/placeholder.jpg';
  const techs = project.acf?.technologies || [];

  return (
    <article className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
      <div className="relative h-48 bg-gray-200">
        {image !== '/placeholder.jpg' ? (
          <Image src={image} alt={title} fill className="object-cover" />
        ) : (
          <div className="flex items-center justify-center h-full text-gray-400">
            No Image
          </div>
        )}
        
        {project.acf?.featured && (
          <span className="absolute top-4 right-4 bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
            Featured
          </span>
        )}
      </div>

      <div className="p-6">
        <h3 className="text-xl font-bold mb-2 hover:text-blue-600 transition">
          <Link href={`/projects/${project.slug}`}>{title}</Link>
        </h3>

        {project.acf?.client && (
          <p className="text-sm text-gray-600 mb-2">Client: {project.acf.client}</p>
        )}

        <p className="text-gray-700 mb-4 line-clamp-3">{excerpt}</p>

        {techs.length > 0 && (
          <div className="flex flex-wrap gap-2 mb-4">
            {techs.slice(0, 3).map((tech: string) => (
              <span key={tech} className="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                {tech}
              </span>
            ))}
            {techs.length > 3 && (
              <span className="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">
                +{techs.length - 3}
              </span>
            )}
          </div>
        )}

        <Link href={`/projects/${project.slug}`} 
          className="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium group">
          View Project
          <svg className="ml-2 w-4 h-4 transform group-hover:translate-x-1 transition" 
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
          </svg>
        </Link>
      </div>
    </article>
  );
}